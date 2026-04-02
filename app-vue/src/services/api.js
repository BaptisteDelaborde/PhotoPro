export const apiGestion = {

  //un photographe crée une galerie
  async creerGalerie(donnees) {
    console.log("POST /galeries", donnees);
    const res = await fetch('/galeries', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token') || ''}`
      },
      body: JSON.stringify(donnees)
    });

    if (!res.ok) {
      const error = await res.json();
      throw new Error(error.error || 'Erreur lors de la création de la galerie');
    }

    return await res.json();
  },

  //un photographe upload une photo
  async uploadPhoto(fichier, galerieId) {
    console.log("POST /photos", fichier.name);
    const formData = new FormData();
    formData.append('file', fichier);
    formData.append('gallery_id', galerieId);

    const res = await fetch('/photos', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token') || ''}`
      },
      body: formData
    });

    if (!res.ok) {
      const error = await res.json();
      throw new Error(error.error || 'Erreur lors de l\'upload');
    }

    return await res.json();
  },

  async post(endpoint, data) {
    const res = await fetch(endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token') || ''}`
      },
      body: JSON.stringify(data)
    });

    if (!res.ok) {
      const error = await res.json();
      throw new Error(error.error || 'Erreur lors de la requête');
    }

    return await res.json();
  },

  // Méthode générique GET
  async get(endpoint) {
    const res = await fetch(endpoint, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token') || ''}`
      }
    });

    if (!res.ok) {
      const error = await res.json();
      throw new Error(error.error || 'Erreur lors de la requête');
    }

    return await res.json();
  }
};

