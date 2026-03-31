// src/services/api.js

export const apiGestion = {
  
  //un photographe crée une galerie
  async creerGalerie(donnees) {
    console.log("Mock appel sortant -> POST /galeries", donnees);
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve({
          status: 201,
          data: { id_galerie: "uuid-galerie-12345" },
          message: "Galerie créée avec succès"
        });
      }, 800);
    });
  },

  //un photographe upload une photo
  async uploadPhoto(fichier) {
    console.log("Mock appel sortant -> POST /photos", fichier.name);
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve({
          status: 201,
          data: { 
            id_photo: "uuid-photo-67890", 
            url_s3: "https://faux-s3-bucket.aws.com/miniature.jpg",
            metadata: { taille: fichier.size }
          }
        });
      }, 1500);
    });
  }
};