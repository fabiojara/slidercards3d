# Script para convertir SVG a PNG usando ImageMagick o servicio online
# Ejecutar: .\convertir-svg-a-png.ps1

Write-Host "Convirtiendo SVG a PNG..." -ForegroundColor Cyan
Write-Host ""

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

# Verificar si ImageMagick está disponible
$magickPath = Get-Command magick -ErrorAction SilentlyContinue

if ($magickPath) {
    Write-Host "ImageMagick encontrado. Convirtiendo..." -ForegroundColor Green
    Write-Host ""
    
    foreach ($icon in $icons) {
        $svgFile = "$($icon.name).svg"
        $pngFile = "$($icon.name).png"
        $size = $icon.size
        
        if (Test-Path $svgFile) {
            Write-Host "Convirtiendo: $svgFile -> $pngFile ($size x $size)..." -NoNewline
            try {
                & magick convert $svgFile -resize "${size}x${size}" -background transparent $pngFile
                Write-Host " OK" -ForegroundColor Green
            }
            catch {
                Write-Host " ERROR" -ForegroundColor Red
            }
        }
        else {
            Write-Host "No se encuentra: $svgFile" -ForegroundColor Yellow
        }
    }
    
    Write-Host ""
    Write-Host "¡Conversión completada!" -ForegroundColor Green
}
else {
    Write-Host "ImageMagick no está instalado." -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Opciones para convertir SVG a PNG:" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "1. Instalar ImageMagick:" -ForegroundColor White
    Write-Host "   Descarga: https://imagemagick.org/script/download.php" -ForegroundColor Gray
    Write-Host ""
    Write-Host "2. Usar servicio online:" -ForegroundColor White
    Write-Host "   Visita: https://cloudconvert.com/svg-to-png" -ForegroundColor Gray
    Write-Host "   Sube cada archivo .svg y descarga como .png" -ForegroundColor Gray
    Write-Host ""
    Write-Host "3. Usar herramienta HTML:" -ForegroundColor White
    Write-Host "   Abre: convertir-svg-a-png.html en tu navegador" -ForegroundColor Gray
    Write-Host ""
}

