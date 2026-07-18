#!/usr/bin/env bash
set -u
CHROME=/home/toninow/bin/google-chrome
BASE="http://127.0.0.1:8123"
OUT="docs/previews"
AUTH=/tmp/abchrome-auth
ANON=/tmp/abchrome-anon
SLUG="mp-proveedores"

mkdir -p "$OUT"
rm -rf "$AUTH" "$ANON"; mkdir -p "$AUTH" "$ANON"

shoot () { # profile w h url out
  "$CHROME" --headless=new --disable-gpu --no-sandbox --user-data-dir="$1" \
    --window-size="$2,$3" --virtual-time-budget=9000 --hide-scrollbars \
    --force-device-scale-factor=1 --screenshot="$OUT/$5" "$4" >/dev/null 2>&1
  echo "  -> $5 ($2x$3)"
}

# Authenticate the AUTH profile.
shoot "$AUTH" 1440 1000 "$BASE/__dev-login" _warmup.png
rm -f "$OUT/_warmup.png"

echo "Public:"
shoot "$ANON" 1440 1000 "$BASE/es"                     home-desktop.png
shoot "$ANON"  768 1024 "$BASE/es"                     home-tablet.png
shoot "$ANON"  390  844 "$BASE/es"                     home-mobile-390.png
shoot "$ANON"  360  800 "$BASE/es"                     home-mobile-360.png
shoot "$ANON" 1440 1000 "$BASE/es/proyectos"          projects-desktop.png
shoot "$ANON"  390  844 "$BASE/es/proyectos"          projects-mobile.png
shoot "$ANON" 1440 1000 "$BASE/es/proyectos/$SLUG"    project-detail-desktop.png
shoot "$ANON"  390  844 "$BASE/es/proyectos/$SLUG"    project-detail-mobile.png

echo "Admin (login page uses anon profile):"
shoot "$ANON" 1440 1000 "$BASE/admin/login"           admin-login-desktop.png
shoot "$ANON"  390  844 "$BASE/admin/login"           admin-login-mobile.png

echo "Admin (authenticated):"
shoot "$AUTH" 1440 1000 "$BASE/admin"                 admin-dashboard-desktop.png
shoot "$AUTH"  390  844 "$BASE/admin"                 admin-dashboard-mobile.png
shoot "$AUTH" 1440 1000 "$BASE/admin/projects/$SLUG/edit"  admin-project-edit-desktop.png
shoot "$AUTH"  390  844 "$BASE/admin/projects/$SLUG/edit"  admin-project-edit-mobile.png
shoot "$AUTH" 1440 1000 "$BASE/admin/site-preview"    admin-site-preview.png
echo "DONE"
