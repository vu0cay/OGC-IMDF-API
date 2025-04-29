import fs from 'fs';
import path from 'path';
import axios from 'axios';

const dataFolder = './data';
const files = [
  'addresses.json',
  'venues.json',
  'buildings.json',
  'footprints.json',
  'levels.json',
  'units.json',
  'amenities.json',
  'anchors.json',
  'occupants.json',
  'details.json',
  'fixtures.json',
  'geofences.json',
  'kiosks.json',
  'openings.json',
  'relationships.json',
  'sections.json'
];

(async () => {
  for (const file of files) {
    const filePath = path.join(dataFolder, file);
    const featureType = path.basename(file, '.json'); // Extracts the feature_type from the filename
    
    try {
      // Read the JSON file
      const fileData = fs.readFileSync(filePath, 'utf-8');
      const jsonData = JSON.parse(fileData);

      // Ensure the file contains a "features" field
      if (jsonData.features) {
        for (const feature of jsonData.features) {
          try {
            const apiUrl = `http://127.0.0.1:8000/api/v1.0.0/${featureType}`;
            console.log("URL:", apiUrl);
            console.log(feature);
            const response = await axios.post(apiUrl, feature);
            console.log(`Posted to ${apiUrl}:`, response.status, response.statusText);
          } catch (postError) {
            console.error(`Failed to post feature ${feature.id} to ${featureType}:`, postError.message);
          }
        }
      } else {
        console.warn(`No features found in ${file}`);
      }
    } catch (error) {
      console.error(`Error processing file ${file}:`, error.message);
    }
  }
})();