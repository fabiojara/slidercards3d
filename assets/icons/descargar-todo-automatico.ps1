# Script completo para descargar SVG y convertirlos a PNG automáticamente
# Ejecutar: .\descargar-todo-automatico.ps1

Write-Host "=== Descarga Automática de Iconos PNG ===" -ForegroundColor Cyan
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

# Paso 1: Descargar SVG
Write-Host "Paso 1: Descargando archivos SVG..." -ForegroundColor Yellow
Write-Host ""

foreach ($icon in $icons) {
    $iconName = $icon.name
    $size = $icon.size
    $url = "${baseUrl}${iconName}.svg?width=${size}&height=${size}&color=%23000000"
    $svgFile = "${iconName}.svg"
    
    if (-not (Test-Path $svgFile)) {
        try {
            Write-Host "  Descargando: $svgFile..." -NoNewline
            Invoke-WebRequest -Uri $url -OutFile $svgFile -ErrorAction Stop | Out-Null
            Write-Host " OK" -ForegroundColor Green
        }
        catch {
            Write-Host " ERROR" -ForegroundColor Red
        }
    }
    else {
        Write-Host "  Ya existe: $svgFile" -ForegroundColor Gray
    }
}

Write-Host ""

# Paso 2: Convertir a PNG
Write-Host "Paso 2: Convirtiendo SVG a PNG..." -ForegroundColor Yellow
Write-Host ""

# Verificar ImageMagick
$magickPath = Get-Command magick -ErrorAction SilentlyContinue

if ($magickPath) {
    Write-Host "  ImageMagick encontrado. Convirtiendo..." -ForegroundColor Green
    Write-Host ""
    
    foreach ($icon in $icons) {
        $svgFile = "$($icon.name).svg"
        $pngFile = "$($icon.name).png"
        $size = $icon.size
        
        if (Test-Path $svgFile) {
            Write-Host "  Convirtiendo: $svgFile -> $pngFile..." -NoNewline
            try {
                & magick convert $svgFile -resize "${size}x${size}" -background transparent $pngFile 2>&1 | Out-Null
                if (Test-Path $pngFile) {
                    Write-Host " OK" -ForegroundColor Green
                }
                else {
                    Write-Host " ERROR" -ForegroundColor Red
                }
            }
            catch {
                Write-Host " ERROR" -ForegroundColor Red
            }
        }
    }
    
    Write-Host ""
    Write-Host "=== ¡Proceso completado! ===" -ForegroundColor Green
    Write-Host ""
    Write-Host "Iconos PNG disponibles:" -ForegroundColor Cyan
    Get-ChildItem *.png | ForEach-Object { Write-Host "  ✓ $($_.Name)" -ForegroundColor Green }
}
else {
    Write-Host "  ImageMagick no está instalado." -ForegroundColor Yellow
    Write-Host ""
    Write-Host "  Abriendo herramienta HTML para conversión..." -ForegroundColor Cyan
    Write-Host ""
    
    # Abrir la herramienta HTML
    $htmlFile = Join-Path $PSScriptRoot "convertir-svg-a-png.html"
    if (Test-Path $htmlFile) {
        Start-Process $htmlFile
        Write-Host "  ✓ Herramienta HTML abierta en el navegador" -ForegroundColor Green
        Write-Host ""
        Write-Host "  Instrucciones:" -ForegroundColor Yellow
        Write-Host "  1. Haz clic en 'Descargar PNG' para cada icono" -ForegroundColor White
        Write-Host "  2. Guarda cada archivo en esta carpeta: $PSScriptRoot" -ForegroundColor White
        Write-Host "  3. Asegúrate de usar los nombres exactos (ej: image.png)" -ForegroundColor White
        Write-Host ""
    }
    else {
        Write-Host "  ERROR: No se encuentra convertir-svg-a-png.html" -ForegroundColor Red
        Write-Host ""
        Write-Host "  Alternativa: Visita https://cloudconvert.com/svg-to-png" -ForegroundColor Yellow
    }
}

Write-Host ""

