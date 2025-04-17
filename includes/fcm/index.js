let admin = require("firebase-admin");
let appAdmin = require('firebase-admin/app');
let messagingAdmin = require('firebase-admin/messaging');
let serviceAccount = require("./firebase.json");

let app = appAdmin.initializeApp({
  credential: admin.credential.cert(serviceAccount),
  projectId: "eqd-delta",
}, "dev.equestria.delta");
//console.log(app);

let messaging = messagingAdmin.getMessaging(app);
//console.log(messaging);

let payload = {
  notification: {
    title: process.argv[3],
    body: process.argv[4],
    icon: "ic_stat_name"
  }
}

console.log(payload);

messaging.sendToDevice(process.argv[2], payload);
