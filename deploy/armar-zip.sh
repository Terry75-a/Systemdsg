#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────
# Arma el zip para subir a cPanel (Asisten + Panel de clientes).
#
#   bash deploy/armar-zip.sh                 → ../asisten-cpanel.zip
#   bash deploy/armar-zip.sh --con-uploads   → incluye fotos de rostro y evidencias
#                                              (solo para la PRIMERA subida)
#
# No toca el proyecto: copia lo necesario a una carpeta temporal, quita las
# dependencias de desarrollo, comprime y borra la temporal.
# El zip nunca lleva .env, logs, sesiones, .git/.svn ni info.php, así que
# se puede descomprimir encima de una versión ya subida sin pisar nada del
# servidor.
# ──────────────────────────────────────────────────────────────
set -euo pipefail

RAIZ="$(cd "$(dirname "$0")/.." && pwd)"
SALIDA="${SALIDA:-$(dirname "$RAIZ")/asisten-cpanel.zip}"
PHP="${PHP:-$(command -v php || echo /opt/lampp/bin/php)}"
COMPOSER="${COMPOSER:-$(command -v composer)}"
CON_UPLOADS=0
if [[ "${1:-}" == "--con-uploads" ]]; then CON_UPLOADS=1; fi

TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT
DEST="$TMP/asisten"
mkdir -p "$DEST"

echo "→ Copiando archivos…"
rsync -a \
  --exclude='public/info.php' \
  --exclude='public/videos/videoooo.mp4' --exclude='public/videos/videoprueba.mp4' --exclude='public/videos/dsg_peru.mp4' \
  --exclude='writable/logs/*' --exclude='writable/debugbar/*' --exclude='writable/session/*' \
  --exclude='writable/cache/*' --exclude='writable/sync/*' --exclude='writable/tmp/*' --exclude='writable/uploads/*' \
  --exclude='.svn' --exclude='.git' --exclude='.env*' --exclude='.DS_Store' \
  --include='app/***' --include='public/***' --include='vendor/***' --include='writable/***' \
  --include='spark' --include='composer.json' --include='composer.lock' --include='preload.php' \
  --exclude='*' "$RAIZ/" "$DEST/"

# Carpetas de writable presentes y vacías (con su index.html de protección)
for d in logs debugbar session cache sync tmp uploads uploads/rostros uploads/evidencias; do
  mkdir -p "$DEST/writable/$d"
  if [[ -f "$RAIZ/writable/$d/index.html" ]]; then cp "$RAIZ/writable/$d/index.html" "$DEST/writable/$d/"; fi
done

if (( CON_UPLOADS )); then
  # Solo las fotos que la base todavía usa (rostro de cada persona y evidencias
  # de marcas/incidencias); las de personas borradas no se llevan.
  echo "→ Incluyendo fotos de rostro y evidencias en uso…"
  if [[ -z "${MYSQL:-}" ]]; then
    if [[ -x /opt/lampp/bin/mysql ]]; then MYSQL=/opt/lampp/bin/mysql; else MYSQL="$(command -v mysql)"; fi
  fi
  BASE="${BASE:-sistemapo}"
  RUTAS="$("$MYSQL" -uroot -N "$BASE" -e "
      SELECT rostro_path FROM users WHERE rostro_path <> ''
      UNION SELECT evidencia FROM attendance WHERE evidencia <> ''
      UNION SELECT evidencia FROM incidents WHERE evidencia <> ''")" \
    || { echo "✘ No se pudo leer la base $BASE (¿MySQL apagado?)"; exit 1; }
  n=0
  while IFS= read -r ruta; do
    [[ -z "$ruta" || "$ruta" == *..* ]] && continue
    if [[ -f "$RAIZ/writable/uploads/$ruta" ]]; then
      mkdir -p "$DEST/writable/uploads/$(dirname "$ruta")"
      cp "$RAIZ/writable/uploads/$ruta" "$DEST/writable/uploads/$ruta"
      n=$((n + 1))
    fi
  done <<< "$RUTAS"
  echo "  $n archivos"
fi

# Archivos de despliegue
cp "$RAIZ/deploy/env.ejemplo"     "$DEST/env.ejemplo"
cp "$RAIZ/deploy/htaccess-raiz"   "$DEST/htaccess-raiz"
cp "$RAIZ/deploy/DESPLIEGUE.md"   "$DEST/DESPLIEGUE.md"

echo "→ Quitando dependencias de desarrollo…"
( cd "$DEST" && "$PHP" "$COMPOSER" install --no-dev --optimize-autoloader --no-interaction --no-scripts --quiet )

echo "→ Comprimiendo…"
rm -f "$SALIDA"
( cd "$TMP" && zip -qr -9 "$SALIDA" asisten )

echo "✔ Listo: $SALIDA ($(du -h "$SALIDA" | cut -f1))"
