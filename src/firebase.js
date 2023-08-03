// Import the functions you need from the SDKs you need
import { initializeApp } from 'firebase/app'
import { getAuth } from 'firebase/auth'
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
const firebaseConfig = {

  apiKey: "AIzaSyB30C1YhFnybKNEI-cEwUG5AH6OZAwJnXE",

  authDomain: "signin-aa060.firebaseapp.com",

  projectId: "signin-aa060",

  storageBucket: "signin-aa060.appspot.com",

  messagingSenderId: "1000435554923",

  appId: "1:1000435554923:web:ddce2b105ef300cc2eef2e"

};


// Initialize Firebase
const app = initializeApp(firebaseConfig)
const auth = getAuth(app)
export default auth
