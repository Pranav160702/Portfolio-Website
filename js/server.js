const express = require('express');
const formidable = require('formidable');
const fs = require('fs');
const path = require('path');

const app = express();
const port = 3000;

// Define the route for handling file uploads
app.post('/upload', (req, res) => {
  const form = formidable({ multiples: false });

  // Parse the form data
  form.parse(req, (err, fields, files) => {
    if (err) {
      console.error('Error parsing form:', err);
      return res.status(500).send('Error uploading file.');
    }

    // Check if the uploaded file is named 'cv.pdf'
    const uploadedFile = files.cv;
    if (!uploadedFile || uploadedFile.name !== 'cv.pdf') {
      return res.status(400).send('Invalid file. Please upload a CV in PDF format.');
    }

    // Define the directory where the file will be saved
    const uploadDir = path.join(__dirname, 'uploads');

    // Create the directory if it doesn't exist
    if (!fs.existsSync(uploadDir)) {
      fs.mkdirSync(uploadDir);
    }

    // Define the new path for the uploaded file
    const newPath = path.join(uploadDir, 'cv.pdf');

    // Move the uploaded file to the 'uploads' directory
    fs.rename(uploadedFile.path, newPath, (err) => {
      if (err) {
        console.error('Error moving file:', err);
        return res.status(500).send('Error uploading file.');
      }

      res.send('CV uploaded successfully.');
    });
  });
});

// Start the server
app.listen(port, () => {
  console.log(`Server is listening on port ${port}`);
});
