// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import axios from 'axios'
import { getStorage } from "firebase/storage";

var firebaseConfig = {
  apiKey: "",
  authDomain: "",
  databaseURL: "",
  projectId: "",
  storageBucket: "",
  messagingSenderId: "",
  appId: "",
  measurementId: ""
};

let storage = null

axios.get('/api/panel/firebase-config', {
  headers: {
    'Authorization': 'Bearer ' + localStorage.getItem('user_token')
  }
}).then(({ data }) => {
  if (data.success) {
    firebaseConfig.apiKey = data.result.apiKey;
    firebaseConfig.authDomain = data.result.authDomain;
    firebaseConfig.databaseURL = data.result.databaseURL;
    firebaseConfig.projectId = data.result.projectId;
    firebaseConfig.storageBucket = data.result.storageBucket;
    firebaseConfig.messagingSenderId = data.result.messagingSenderId;
    firebaseConfig.appId = data.result.appId;
    firebaseConfig.measurementId = data.result.measurementId;
  }

  const app = initializeApp(firebaseConfig);
  storage = getStorage(app);
})

// Initialize Firebase
// const app = initializeApp(firebaseConfig);


// const storage = getStorage(app);

export { storage }
