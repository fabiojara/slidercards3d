/**
 * Script Node.js para descargar iconos PNG de Lucide
 *
 * Requiere: npm install lucide
 * Ejecutar: node descargar-con-node.js
 */

const fs = require('fs');
const path = require('path');
const https = require('https');

const icons = [
    { name: 'image', size: 24 },
    { name: 'file-text', size: 24 },
    { name: 'settings', size: 24 },
    { name: 'chevron-left', size: 24 },
    { name: 'chevron-right', size: 24 },
    { name: 'x', size: 24 },
    { name: 'check', size: 24 },
    { name: 'external-link', size: 16 }
];

function downloadIcon(iconName, size) {
    return new Promise((resolve, reject) => {
        const url = `https://api.iconify.design/lucide/${iconName}.svg?width=${size}&height=${size}&color=%23000000`;
        const filePath = path.join(__dirname, `${iconName}.svg`);

        https.get(url, (response) => {
            if (response.statusCode !== 200) {
                reject(new Error(`Error ${response.statusCode}`));
                return;
            }

            const fileStream = fs.createWriteStream(filePath);
            response.pipe(fileStream);

            fileStream.on('finish', () => {
                fileStream.close();
                console.log(`✓ Descargado: ${iconName}.svg`);
                resolve(filePath);
            });
        }).on('error', (err) => {
            reject(err);
        });
    });
}

async function main() {
    console.log('Descargando iconos SVG de Lucide...\n');

    for (const icon of icons) {
        try {
            await downloadIcon(icon.name, icon.size);
        } catch (error) {
            console.error(`✗ Error con ${icon.name}:`, error.message);
        }
    }

    console.log('\n¡SVG descargados!');
    console.log('Ahora convierte los SVG a PNG usando:');
    console.log('https://cloudconvert.com/svg-to-png');
    console.log('\nO instala ImageMagick y ejecuta:');
    console.log('for %f in (*.svg) do magick convert %f -resize 24x24 %~nf.png');
}

main().catch(console.error);

