# Script PowerShell para descargar iconos SVG de Lucide
# Ejecutar: .\descargar-iconos.ps1

Write-Host "Descargando iconos SVG de Lucide..." -ForegroundColor Cyan
Write-Host ""

# Configurar TLS
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

$icons = @(
    @{name='image'; size=24},
    @{name='file-text'; size=24},
    @{name='settings'; size=24},
    @{name='chevron-left'; size=24},
    @{name='chevron-right'; size=24},
    @{name='x'; size=24},
    @{name='check'; size=24},
    @{name='external-link'; size=16}
)

$baseUrl = "https://api.iconify.design/lucide/"

foreach ($icon in $icons) {
    $iconName = $icon.name
    $size = $icon.size
    $url = "${baseUrl}${iconName}.svg?width=${size}&height=${size}&color=%23000000"
    $outputFile = "${iconName}.svg"

    try {
        Write-Host "Descargando: $iconName.svg ($size x $size)..." -NoNewline
        Invoke-WebRequest -Uri $url -OutFile $outputFile -ErrorAction Stop
        Write-Host " OK" -ForegroundColor Green
    }
    catch {
        Write-Host " ERROR: $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "SVG descargados!" -ForegroundColor Green
Write-Host ""
Write-Host "Ahora convierte los SVG a PNG usando:" -ForegroundColor Yellow
Write-Host "1. Visita: https://cloudconvert.com/svg-to-png" -ForegroundColor White
Write-Host "2. O usa ImageMagick si lo tienes instalado" -ForegroundColor White
Write-Host ""
