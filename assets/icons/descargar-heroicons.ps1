# Script PowerShell para descargar iconos SVG de Heroicons
# Heroicons es la librería oficial de Tailwind CSS, perfecta para diseños estilo Vercel/Linear/Stripe/Apple
# Ejecutar: .\descargar-heroicons.ps1

Write-Host "=== Descargando Iconos Heroicons ===" -ForegroundColor Cyan
Write-Host "Heroicons - Librería oficial de Tailwind CSS" -ForegroundColor Gray
Write-Host ""

# Configurar TLS
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

# Iconos seleccionados para diseño moderno estilo Vercel/Linear/Stripe/Apple
# Usamos la variante "outline" que es más minimalista y moderna
$icons = @(
    @{name='photo'; size=24; description='Imágenes'},
    @{name='document-text'; size=24; description='Páginas'},
    @{name='cog-6-tooth'; size=24; description='Configuración'},
    @{name='chevron-left'; size=24; description='Anterior'},
    @{name='chevron-right'; size=24; description='Siguiente'},
    @{name='x-mark'; size=24; description='Cerrar'},
    @{name='check'; size=24; description='Check'},
    @{name='arrow-top-right-on-square'; size=16; description='Enlace externo'}
)

$baseUrl = "https://api.iconify.design/heroicons/"

Write-Host "Descargando iconos SVG (variante outline)..." -ForegroundColor Yellow
Write-Host ""

foreach ($icon in $icons) {
    $iconName = $icon.name
    $size = $icon.size
    $description = $icon.description

    # Heroicons en Iconify usa formato: heroicons-outline:icon-name o heroicons:icon-name-24-outline
    # Probamos ambos formatos
    # Formato 1: heroicons-outline:photo
    # Formato 2: heroicons:photo-24-outline

    # Usamos la variante outline (más minimalista)
    # Iconify API acepta: heroicons-outline/photo o heroicons/photo-24-outline
    $iconifyName = "${iconName}"
    $url = "https://api.iconify.design/heroicons-outline/${iconifyName}.svg?width=${size}&height=${size}&color=%23000000"
    $outputFile = "${iconName}.svg"

    try {
        Write-Host "  [$description] Descargando: $iconName.svg ($size x $size)..." -NoNewline
        $response = Invoke-WebRequest -Uri $url -OutFile $outputFile -ErrorAction Stop

        # Verificar que el archivo se descargó correctamente
        if (Test-Path $outputFile -PathType Leaf) {
            $fileSize = (Get-Item $outputFile).Length
            if ($fileSize -gt 0) {
                Write-Host " OK" -ForegroundColor Green
            }
            else {
                Write-Host " ERROR (archivo vacío)" -ForegroundColor Red
                Remove-Item $outputFile -ErrorAction SilentlyContinue
            }
        }
        else {
            Write-Host " ERROR" -ForegroundColor Red
        }
    }
    catch {
        Write-Host " ERROR: $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "=== SVG descargados ===" -ForegroundColor Green
Write-Host ""
Write-Host "Próximo paso: Convertir SVG a PNG" -ForegroundColor Yellow
Write-Host ""
Write-Host "Opciones:" -ForegroundColor Cyan
Write-Host "1. Abrir herramienta HTML: .\convertir-svg-a-png.html" -ForegroundColor White
Write-Host "2. Usar servicio online: https://cloudconvert.com/svg-to-png" -ForegroundColor White
Write-Host "3. Si tienes ImageMagick: .\convertir-svg-a-png.ps1" -ForegroundColor White
Write-Host ""

