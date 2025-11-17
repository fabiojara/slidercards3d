@echo off
echo Descargando iconos PNG de Lucide...
echo.

REM Crear directorio si no existe
if not exist "assets\icons" mkdir "assets\icons"
cd assets\icons

echo Descargando iconos desde Lucide Icons...
echo.

REM Usar PowerShell para descargar los iconos desde un servicio que los provea
powershell -Command "& {[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://api.iconify.design/lucide/image.svg?width=24&height=24' -OutFile 'image.svg'}"
powershell -Command "& {[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://api.iconify.design/lucide/file-text.svg?width=24&height=24' -OutFile 'file-text.svg'}"
powershell -Command "& {[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://api.iconify.design/lucide/settings.svg?width=24&height=24' -OutFile 'settings.svg'}"
powershell -Command "& {[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://api.iconify.design/lucide/chevron-left.svg?width=24&height=24' -OutFile 'chevron-left.svg'}"
powershell -Command "& {[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://api.iconify.design/lucide/chevron-right.svg?width=24&height=24' -OutFile 'chevron-right.svg'}"
powershell -Command "& {[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://api.iconify.design/lucide/x.svg?width=24&height=24' -OutFile 'x.svg'}"
powershell -Command "& {[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://api.iconify.design/lucide/check.svg?width=24&height=24' -OutFile 'check.svg'}"
powershell -Command "& {[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://api.iconify.design/lucide/external-link.svg?width=16&height=16' -OutFile 'external-link.svg'}"

echo.
echo SVG descargados. Ahora convierte los SVG a PNG usando:
echo https://cloudconvert.com/svg-to-png
echo.
echo O usa ImageMagick si lo tienes instalado.
echo.
pause

