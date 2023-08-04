<template>
  <div class="d-flex justify-content-between">
    <div>
      <h1 class="mt-4">Welcome to Dashboard</h1>
      <p>Hello, {{ user.displayName }}</p>
      <p>Email: {{ user.email }}</p>
    </div>
    <div class="mt-5"><button class="btn btn-danger" @click="logout">Logout</button></div>
  </div>

  <form class="mt-2">
    <div class="form-group">
      <label for="sender-email">Sender Email:</label>
      <input
        type="email"
        class="form-control"
        v-model="senderEmail"
        id="sender-email"
        name="sender-email"
        required
      />
    </div>
    <div class="form-group mt-3">
      <label for="sender-password">Sender Password:</label>
      <input
        type="password"
        class="form-control"
        v-model="senderPassword"
        id="sender-password"
        name="sender-password"
        required
      />
      <small
        >How to get password?<a href="https://support.google.com/accounts/answer/185833?hl=en">
          Here</a
        ></small
      >
    </div>
    <div class="form-group mt-3">
      <label for="sender-nickname">Sender Nickname:</label>
      <input
        type="text"
        class="form-control"
        v-model="senderNickname"
        id="sender-nickname"
        name="sender-nickname"
        required
      />
    </div>
    <div class="form-group mt-3">
      <label for="subject">Subject :</label>
      <input
        type="text"
        class="form-control"
        v-model="subject"
        id="subject"
        name="subject"
        required
      />
    </div>
    <div class="form-group mt-3">
      <label for="receiver-email">Receiver Email:</label>
      <textarea
        class="form-control"
        v-model="receiverEmail"
        id="receiver-email"
        name="receiver-email"
        rows="3"
        required
      ></textarea>
      <small class="form-text text-muted"
        >Enter multiple email addresses separated by commas or new lines.</small
      >
      <div class="form-group mt-3">
        <label for="image-files">Upload Images:</label>
        <input
          type="file"
          accept="image/*"
          @change="handleImageUpload"
          multiple
          class="form-control"
          id="image-files"
          name="image-files"
          required
        />
        <small class="form-text text-muted">Upload images to include in the email body.</small>
      </div>
    </div>
    <div class="form-group mt-3">
      <label for="email-body">Email Body:</label>
      <textarea
        class="form-control"
        v-model="emailBody"
        id="email-body"
        name="email-body"
        required
      ></textarea>
      <small>Use {image1} , {image2}, {image3},... to insert image</small>
    </div>
    <div class="mt-3 d-flex justify-content-around">
      <button type="button" class="btn btn-primary mt-2" @click="updatePreview">Preview</button>
      <input type="button" class="btn btn-primary mt-2" value="Send email" @click="sendFormData" />
    </div>
  </form>

  <div class="card mt-3">
    <div id="preview-pane" class="card-body mt-1" v-html="previewHtml"></div>
  </div>

  <h2 class="mt-3">Result</h2>
  <div v-if="responseData" class="alert alert-success card mt-3" role="alert">
    Response: {{ responseData }}
  </div>
  <div v-if="error" class="alert alert-danger card mt-3" role="alert">Error: {{ error }}</div>
  <div class="d-flex justify-content-end">
    <button type="reset" class="btn btn-warning mt-2 mb-5" @click="resetForm">Reset</button>
  </div>
</template>

<script>
import { ref } from 'vue'
import { signOut } from 'firebase/auth'
import auth from '../firebase'
import router from '../router'
import axios from 'axios'

export default {
  data() {
    return {
      user: '',
      senderEmail: '',
      senderPassword: '',
      senderNickname: '',
      receiverEmail: [],
      subject: '',
      emailBody: '',
      imageFiles: [], // New property to store the uploaded image file
      previewHtml: '',
      responseData: '',
      error: ''
    }
  },
  mounted() {
    this.updatePreview() // Update the preview when the component is mounted
  },
  created() {
    // Listen for user state changes
    auth.onAuthStateChanged((user) => {
      if (user) {
        this.user = user
      } else {
        // If the user is not logged in, redirect to the login page
        router.push('/login')
      }
    })
  },
  methods: {
    async handleImageUpload(event) {
      this.imageFiles = [] // Clear the imageFiles array before handling new uploads
      const files = event.target.files
      if (files && files.length) {
        // Loop through the uploaded files and process them one by one
        for (let i = 0; i < files.length; i++) {
          const file = files[i]

          // Use a FileReader to read the file as a data URL
          const dataUrl = await this.readFileAsDataURL(file)

          // Resize the image
          const resizedDataUrl = await this.resizeImage(dataUrl, 800, 600)

          // Add the resized data URL to the imageFiles array
          this.imageFiles.push(resizedDataUrl)
        }
      }
    },

    readFileAsDataURL(file) {
      return new Promise((resolve) => {
        const reader = new FileReader()
        reader.onload = (event) => {
          resolve(event.target.result)
        }
        reader.readAsDataURL(file)
      })
    },

    resizeImage(dataUrl, maxWidth, maxHeight) {
      return new Promise((resolve) => {
        const img = new Image()
        img.onload = () => {
          const canvas = document.createElement('canvas')
          const ctx = canvas.getContext('2d')

          let width = img.width
          let height = img.height

          if (width > maxWidth) {
            height *= maxWidth / width
            width = maxWidth
          }

          if (height > maxHeight) {
            width *= maxHeight / height
            height = maxHeight
          }

          canvas.width = width
          canvas.height = height
          ctx.drawImage(img, 0, 0, width, height)

          const resizedDataUrl = canvas.toDataURL()
          console.log('Resized Image Dimensions:', width, height) // Add this line
          resolve(resizedDataUrl)
        }
        img.src = dataUrl
      })
    },
    updatePreview() {
      // Replace all occurrences of '{imageX}' in the email body with img tags containing the uploaded images
      let emailBodyWithImages = this.emailBody
      for (let i = 0; i < this.imageFiles.length; i++) {
        // Use a regular expression to replace all occurrences of {imageX} with the actual image tag
        const placeholderRegExp = new RegExp(`{image${i + 1}}`, 'g')
        emailBodyWithImages = emailBodyWithImages.replace(
          placeholderRegExp,
          `<img src="${this.imageFiles[i]}" alt="Image">`
        )
      }
      // If there are any remaining {imageX} placeholders without uploaded images, remove them
      const placeholderRegExp = /{image\d+}/g
      emailBodyWithImages = emailBodyWithImages.replace(placeholderRegExp, '')

      this.previewHtml = emailBodyWithImages
    },

    resetForm() {
      this.senderNickname = ''
      this.subject = '' // Use an equal sign, not a comma
      this.emailBody = ''
      this.imageFiles = []
      this.previewHtml = ''
    },
    logout() {
      signOut(auth)
        .then(() => {
          // Handle successful sign-out
          this.user = null
          router.push('/login')
        })
        .catch((error) => {
          // Handle sign-out error
          console.error('Sign-out error:', error)
        })
    },

    // Assuming this.imageFiles is an array of base64 image data strings
    async sendFormData() {
      // Prepare the data to be sent
      const formData = new FormData()
      formData.append('previewHtml', this.emailBody)
      formData.append('senderEmail', this.senderEmail)
      formData.append('subject', this.subject)
      formData.append('senderNickname', this.senderNickname)
      formData.append('senderPassword', this.senderPassword)
      formData.append('receiverEmail', this.receiverEmail)

      // Convert base64 image data to Blob objects and append them to the FormData
      this.imageFiles.forEach((base64ImageData, index) => {
        // Remove the data:image/png;base64, prefix from the base64 data if present
        const base64Data = base64ImageData.replace(/^data:image\/(png|jpeg|jpg);base64,/, '')

        // Convert the base64 data to a Blob object
        const byteCharacters = atob(base64Data)
        const byteArrays = []
        for (let offset = 0; offset < byteCharacters.length; offset += 512) {
          const slice = byteCharacters.slice(offset, offset + 512)
          const byteNumbers = new Array(slice.length)
          for (let i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i)
          }
          const byteArray = new Uint8Array(byteNumbers)
          byteArrays.push(byteArray)
        }
        const blob = new Blob(byteArrays, { type: 'image/png' }) // Change the type if needed
        console.log(blob)
        // Append the Blob to the FormData with the same key 'images[]'
        formData.append('images[]', blob, `image${index + 1}.png`)
      })

      try {
        // Send the data to the PHP script using Axios
        const response = await axios.post('https://backendphp.hailecv.site/test.php', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        console.log('Response:', response.data)
        if (response.data.status === 'error') {
          this.error = response.data.message
          this.responseData = ''
        } else if (response.data.status === 'success') {
          this.responseData = response.data.message
          this.error = ''
        }
      } catch (error) {}
    }
  }
}
</script>

<style>
/* Your custom styles here */
</style>
