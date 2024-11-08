function generateMapTiles() {
  const input = document.getElementById('imageInput');
  const file = input.files[0];

  if (file) {
    const reader = new FileReader();

    reader.onload = function (e) {
      const img = new Image();
      img.src = e.target.result;

      img.onload = function () {
        generateTilesAndDownload(img);
      };
    };

    reader.readAsDataURL(file);
  }
}

function generateTilesAndDownload(image) {
  const tileSize = 256;
  const zoomLevel = 18;

  const numTilesX = Math.pow(2, zoomLevel);
  const numTilesY = Math.pow(2, zoomLevel);

  const zip = new JSZip();

  for (let x = 0; x < numTilesX; x++) {
    for (let y = 0; y < numTilesY; y++) {
      const canvas = document.createElement('canvas');
      canvas.width = tileSize;
      canvas.height = tileSize;
      const context = canvas.getContext('2d');

      // Draw the corresponding portion of the image onto the canvas
      context.drawImage(
        image,
        (x * tileSize) / numTilesX,
        (y * tileSize) / numTilesY,
        tileSize / numTilesX,
        tileSize / numTilesY,
        0,
        0,
        tileSize,
        tileSize
      );

      // Save the canvas as a Blob
      canvas.toBlob(function (blob) {
        // Add the Blob to the zip file with the specified format
        const filename = `${zoomLevel}_${x}_${y}.png`;
        zip.file(filename, blob);

        // If all tiles have been processed, create the zip file and initiate download
        if (x === numTilesX - 1 && y === numTilesY - 1) {
          zip.generateAsync({ type: 'blob' }).then(function (content) {
            // Save the zip file or perform further actions
            saveAs(content, `map_tiles_${zoomLevel}.zip`);
          });
        }
      });
    }
  }
}
