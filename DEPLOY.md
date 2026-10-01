# Deploying HeyEvent to AWS EC2

Production deployment for the `heyevent` Laravel app using Docker Compose on a
single EC2 instance. Everything below assumes **Ubuntu 24.04 LTS** as the host OS.

---

## 1. What gets built

`deploy.sh` produces a three-service stack on one host:

| Service | Image | Role |
|---|---|---|
| `app` | `heyevent/app` | Nginx + PHP-FPM running Laravel, serves HTTP |
| `queue` | `heyevent/app` | Runs `php artisan queue:work` for database jobs |
| `mysql` | `mysql:8.4` | MySQL database with a named volume |

Nginx listens on the host, so **do not run Apache or a separate nginx on the host.**
If the host already has a web server on port 80, stop it before deploying.

---

## 2. Cost: confirm your credit before you start

Free-tier entitlements depend on when the AWS account was created, and AWS has
changed these policies more than once. Verify against your own account rather
than trusting any generic guide:

- **AWS Billing → Credits** shows remaining promotional credits and their expiry.
- **AWS Billing → Free tier** shows the allowances attached to your account.
- Check whether `t3.small` is covered. It has historically **not** been part of the
  always-free EC2 allowance (that covered `t2.micro`/`t3.micro` for older accounts).

### Sizing

Pick the smallest instance that comfortably holds all three containers.

| | `t3.micro` (1 GB) | `t3.small` (2 GB) |
|---|---|---|
| MySQL 8.4 (`innodb_buffer_pool_size=128M`) | ~350 MB | ~350 MB |
| PHP-FPM + Laravel | ~200 MB | ~250 MB |
| Nginx | ~20 MB | ~20 MB |
| Queue worker | ~150 MB | ~150 MB |
| OS + Docker | ~250 MB | ~250 MB |
| **Total** | **~970 MB — no headroom** | **~1 GB of 2 GB free** |

`t3.micro` technically boots, but with no swap buffer and Burstable CPU credits,
MySQL will be the first thing to suffer. Use `t3.small` if any credit is
available. If you are forced onto `t3.micro`, add swap and cut
`innodb_buffer_pool_size` to `64M`.

Create a **Cost Explorer** budget alarm at $5 before you launch so an idle instance
or an oversized volume cannot bill unnoticed.

---

## 3. Create the instance

EC2 → Instances → Launch instance:

- **Name:** `heyevent-prod`
- **AMI:** Ubuntu Server 24.04 LTS (x86_64)
- **Instance type:** `t3.small` (or `t3.micro` per §2)
- **Key pair:** create or select an existing key; you need the `.pem`
- **Storage:** 20 GB `gp3`, delete-on-termination **enabled**
- **Security group:** create new, see below

### Security group inbound rules

| Port | Source | Purpose |
|---|---|---|
| 22 | your IP only (e.g. `203.0.113.4/32`) | SSH |
| 80 | `0.0.0.0/0` | HTTP (needed for ACME challenges and redirects) |
| 443 | `0.0.0.0/0` | HTTPS |

Do **not** open 3306. MySQL is only reachable on the internal Docker network.

Connect:

```bash
ssh -i ~/.ssh/heyevent.pem ubuntu@<instance-public-ip>
```

Then run the rest of this guide **on the instance**.

---

## 4. Install Docker

```bash
sudo apt-get update
sudo apt-get install -y ca-certificates curl git
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg \
  | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
sudo chmod a+r /etc/apt/keyrings/docker.gpg

echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" \
  | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io \
                        docker-buildx-plugin docker-compose-plugin
sudo usermod -aG docker "$USER"
newgrp docker        # or log out and back in
docker run --rm hello-world
docker compose version
```

`docker-compose-plugin` is required — `deploy.sh` uses `docker compose`.

---

## 5. Get the code

```bash
sudo mkdir -p /srv/heyevent && sudo chown "$USER":"$USER" /srv/heyevent
git clone https://github.com/esterphilipo2020/heyevent.git /srv/heyevent
cd /srv/heyevent
```

The deployment files (`Dockerfile`, `docker-compose.yml`, `deploy.sh`, `docker/`,
`scripts/`, `.env.production.example`, `.dockerignore`) need to be in the repo. If
they are not committed yet, commit them locally and push, or copy them across:

```bash
# from your workstation, if the files are not yet in the repo
scp -r Dockerfile docker-compose.yml deploy.sh docker scripts .env.production.example .dockerignore \
    ubuntu@<instance-ip>:/srv/heyevent/
```

---

## 6. Configure the environment

```bash
cd /srv/heyevent
cp .env.production.example .env.production
```

Edit `.env.production` and set at least:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=http://<instance-ip>          # switch to https:// once TLS is up

DB_DATABASE=heyevent
DB_USERNAME=heyevent
DB_PASSWORD=<strong password>
DB_ROOT_PASSWORD=<different strong password>
```

`deploy.sh` generates `APP_KEY`, `DB_PASSWORD`, and `DB_ROOT_PASSWORD` automatically
on first run if they are still placeholders, so you can also just run `./deploy.sh`
and edit `APP_URL` afterwards. Keep the file mode tight:

```bash
chmod 600 .env.production
```

Before going live, set `MAIL_MAILER=smtp` with SES SMTP credentials — see §9.
Until then it defaults to `log`, which silently discards all mail.

---

## 7. Deploy

```bash
cd /srv/heyevent
./deploy.sh
```

The script builds the image, starts the stack, waits for the MySQL health check,
runs migrations, clears caches, and restarts the queue worker. It is safe to
re-run — that is how you deploy updates.

Watch for:

```
==> Migrating
   INFO  Running migrations.
  0001_01_01_000000_create_users_table .................. DONE
   INFO  Configuration cached successfully.
==> Waiting for http://localhost/up
==> Application is up
```

Then confirm:

```bash
curl -i http://localhost/up          # expect 200
curl -I http://localhost/            # expect 200
curl -I http://localhost/dashboard   # expect 302 -> /login
```

---

## 8. Point DNS at the instance

Add an **A record** for your domain pointing at the instance's public IP, then
propagate. If you are on the raw IP for now, test over HTTP first
(`http://<ip>`) before adding DNS, so you can separate app problems from
DNS/proxy problems.

---

## 9. HTTPS

Out of the box the stack serves **plain HTTP only**, and `.env.production.example`
ships with `SESSION_SECURE_COOKIE=false`. Serving over plain HTTP in production
means session cookies travel in the clear.

Enable TLS with certbot on the host. Because port 80 is owned by the app container,
use `webroot` mode against a directory shared with the container.

1. Append an ACME location to `docker/nginx/default.conf`, inside `server {}`:

   ```nginx
   location ^~ /.well-known/acme-challenge/ {
       root /var/www/certbot;
   }
   ```

2. In `docker-compose.yml`, mount that directory into both services:

   ```yaml
   volumes:
     - certbot_webroot:/var/www/certbot
   ```

   and declare `certbot_webroot:` in the top-level volumes.

3. Bring the stack down and up, install certbot, and issue:

   ```bash
   sudo apt-get install -y certbot
   docker compose --env-file .env.production down
   sudo certbot certonly --standalone \
     --preferred-challenges http --http-01-port 80 \
     -d yourdomain.com -d www.yourdomain.com
   docker compose --env-file .env.production up -d
   ```

4. In `docker/nginx/default.conf`, redirect port 80 to HTTPS and add the TLS
   server block using `/etc/letsencrypt/live/yourdomain.com/{fullchain,privkey.pem}`,
   mounting `/etc/letsencrypt` read-only into the `app` service.

5. Renewal — stop the app briefly so port 80 is free, then restart:

   ```bash
   echo '17 3 * * 0 cd /srv/heyevent && docker compose --env-file .env.production down && sudo certbot renew && docker compose --env-file .env.production up -d' \
     | sudo crontab -
   ```

6. Finally set `SESSION_SECURE_COOKIE=true` in `.env.production` and re-run
   `./deploy.sh` so the cached config picks it up.

The renewal dance causes a few seconds of downtime twice a day. If that matters,
move the certificate issuance and renewal inside Compose as a `certbot` service
using `webroot`, so nginx keeps serving throughout.

---

## 10. Outbound mail

`MAIL_MAILER=log` means password-reset and verification emails are written to the
container log and never delivered. To send real mail, use SES SMTP:

1. Verify a domain or address in SES and request production access.
2. Create an SES SMTP credential set.
3. In `.env.production`:

   ```dotenv
   MAIL_MAILER=smtp
   MAIL_HOST=email-smtp.<region>.amazonaws.com
   MAIL_PORT=587
   MAIL_USERNAME=<SES SMTP username>
   MAIL_PASSWORD=<SES SMTP password>
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="no-reply@yourdomain.com"
   MAIL_FROM_NAME="${APP_NAME}"
   ```

4. Re-run `./deploy.sh`.

If the instance is in a region different from your SES region, SES will reject the
credentials — use the SMTP host for the instance's own region.

---

## 11. Backups

`scripts/backup-db.sh` dumps MySQL and optionally uploads to S3. MySQL data lives
in a Docker named volume, so it is **not** covered by instance snapshots. Schedule
it with cron:

```bash
chmod +x scripts/backup-db.sh
15 3 * * * cd /srv/heyevent && ./scripts/backup-db.sh >> /var/log/heyevent-backup.log 2>&1
```

Local dumps accumulate under `backups/` and are **not** pruned automatically. Either
prune them yourself or rely on an S3 lifecycle rule. Test a restore occasionally:

```bash
./scripts/restore-db.sh backups/heyevent-YYYYmmdd-HHMMSS.sql.gz   # requires --force
```

The restore script refuses to run without `--force` because it drops the target
database.

---

## 12. Updating

```bash
cd /srv/heyevent
git pull
./deploy.sh
```

`deploy.sh` rebuilds the image and re-runs migrations. Anything cached in config
is rebuilt, so do not hand-edit cached config on the server.

---

## 13. Day-to-day operations

```bash
cd /srv/heyevent

docker compose --env-file .env.production ps
docker compose --env-file .env.production logs -f app
docker compose --env-file .env.production logs -f queue
docker compose --env-file .env.production exec app php artisan about

docker compose --env-file .env.production restart app
docker compose --env-file .env.production down      # stop, keep volumes
```

Restarting the instance is safe: volumes are named and persist, MySQL restarts with
the stack, and the containers carry `restart: unless-stopped`.

---

## 14. Troubleshooting

| Symptom | Cause / fix |
|---|---|
| `502 Bad Gateway` | PHP-FPM crashed or hit `pm.max_children`. Check `logs app`. |
| `Connection refused` on 3306 | MySQL not healthy. `logs mysql`; check the volume is not full. |
| `SQLSTATE[HY000] [1045]` | `DB_PASSWORD` and `MYSQL_PASSWORD` disagree. The password is only applied when the volume is **first** initialised — see below. |
| Blank page | `APP_DEBUG=false` hides errors. Temporarily set `true`, `docker compose up -d --force-recreate app`, then revert. |
| Out of disk | `df -h`. The Docker volume and images share the 20 GB root disk; grow the volume or prune old images. |
| Stale JS/CSS after deploy | Browser cache. Assets are fingerprinted; confirm `public/build/manifest.json` exists in the image. |

**Important:** changing `.env.production` after the first run will **not** change
the MySQL root password, because the entrypoint only honours `MYSQL_ROOT_PASSWORD`
when the data directory is empty. To change it on an existing deployment, run the
`ALTER USER` statement by hand:

```bash
docker compose --env-file .env.production exec -T mysql \
  mysql -u root -p'old-password' \
  -e "ALTER USER 'root'@'localhost' IDENTIFIED BY 'new-password'; FLUSH PRIVILEGES;"
```

---

## 15. What was verified, and what was not

Verified against the real stack: Composer install (99 packages), `npm ci`,
Vite/Vite+ build (3289 modules) producing a valid manifest, `package:discover`,
`config:cache` / `route:cache` / `view:cache`, `storage:link`, PHP-FPM and Nginx
config syntax, HTTP 200 on `/up`, `/`, `/login`, `/register`, HTTP 302 to `/login`
for authenticated-only routes, 403 on `.env` and `.git`, immutable caching headers
on fingerprinted assets, Inertia mount-point and `data-page` markup, and MySQL 8.4
starting successfully with the Compose environment mapping.

Not exercised end to end: the final `docker build` and `docker compose up` were
run on a host whose Docker storage driver could not hold the layered image, so the
first real `docker build` will be the first full build. If it fails, read the error
before assuming the configuration is wrong — the individual build steps are
independently verified.

Also untested: TLS, SES delivery, and the backup cron schedule.