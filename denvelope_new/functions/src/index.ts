import * as functions from "firebase-functions";
import * as admin from "firebase-admin";

admin.initializeApp({
    credential: admin.credential.cert({
        projectId: "denvelope-firebase",
        clientEmail: "firebase-adminsdk-gmulz@denvelope-firebase.iam.gserviceaccount.com",
        privateKey: "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDBJGRcrB331RRY\nFQIxLjiwzjgwR7KZFfHnas+8I5St0JXdWqhmldSUIGfi3ELuB0DowCBQp85/vQeV\nZ7e5s7H0mpSeISidlJvy8XQfuph9KkqNnZ33RVpLgMiAgT4Ff2MfCoVtDaksKelw\nIljVhBP1VO68k+82vwAz4idS3E7/BgAphLD548a+PCuIxwJCPh6xMU6AcWB/gTs3\nhP3Brn6JZKs0wUW7msnmleK+xz+wmGBQdXseJBs1GRP87G82Nof5URnSVucZtbBL\nb0JxWI9cG6YOQFAwVTBNG7AvKDyxqDcH9Rks/P4QLWMJQmxX/2W/kJzdbfn5a35z\nEB8yse+VAgMBAAECggEAB1XTffWHedcc26+IkseXnZdnaOYAMXI06r4Sv6l2Rxep\n6Ws9BBBir3F/oI0O+a1K2MwbLhUBcbaDKDh1MNdTRd/tDwhhW4K+XX6wE1hp3C8T\n/8bhrfdzU3TDqKW/7kOFRzOGF7syFpxM4lZbm1hVzbZ5Dq5rol+kqVPIfIwjb+oA\nf2Fq3/p7V8DFkmILGSuYsQ8jfKItgBps4TRNNUz74dY1cqD/b/yKg19ks5WjW3tE\nXC9CkuMMcT3/aoXqjllOWpfKFtEWqzt4m6JmcjaGlJq4fe6ErzJYxSPRaXKHfTln\nf/aGYVboj8yaxODAvUUTkD3dzSCXpdDDGEMNg4S0bQKBgQDuQsT5uNw8HKAqbKIJ\n3AHjY5S8xOpyVALYV5UlA0frT9wL8g3ifs+61IC28lIQozIcA4erA4+yQCQ1imwE\nWHPbo6VZdeR55KURQS8tMSlzyIRcJJEUkO2ZStc6gKL8xbaf4WPah/AOWY0wZGtu\n5+/dvabtN1SOZjB7F9vs3QEL9wKBgQDPhaoxgUaOnk5ri+HCEjxzmVQa28EhkTj2\nZ0WvpMVIkn3lbDUT8JToM4T21PWnEaeygxGHEDr/TCFWHTPjEti21gQia4xLpPRA\nGKYavd9rEXOWx4lR3j6OHQ0rzmktbv0jZ1ym2SrGFpHyGxEJxxfG0VJ9C8es8ohe\nEdrXQGDF0wKBgQCA6HsuKUhu9YXqri/VacVTw9L7dwbpqG6Jook9J1NIC0Mg62t7\nueSmTsb9NIoWGlXINli2vAyJAo8UsLnUeA7nLNkPV+uvz3dHqJ7fspOc40ZJnDcq\ni2ch4w4jxuloHYi7Y/TkjVviR61OG2bM8IwkyrF/8sm66asdTkFdHA2u7QKBgHF/\n5anVzsVKCBICG6ONXcrL3ZgjAOpfBwydRxvdvpkGphzIpe8V8htdsideKk0J9vJH\nhVqdoebnuIPkzPAw6p51RyWcNMtamMxFrvOJTZy9mr6ou44vQ39unDmNVzEmNwUa\nDy6iMCbPSVtlOlhir7pp2ffSbY9FxxfMJCj+aO9JAoGBAJ4z/Z8Qf/jpF/PvO5cV\nBi0iPmRwZ3E/yuT1Gl90jHsCCTWXEXyA3qfbXKOitKcXtrQKtUFbKtet2hkxPuZT\ne73KUU9NQXO+r6vbAbTX0fgSZad5vvxwzu50N3NstxM4ivOGsYnAFnBfXP3exyXC\ncTcXeGOkGsjfDoaWtdmRHfkO\n-----END PRIVATE KEY-----\n",
    }),
    databaseURL: "https://denvelope-firebase.firebaseio.com",
    storageBucket: "denvelope-firebase.appspot.com",
});

const region = "europe-west1";

export const UserCreated = functions.region(region).auth.user().onCreate(user => {
    void admin.firestore().collection("users").doc(user.uid).set({
        created: Math.round(Date.now() / 1000),
        usedStorage: 0,
    });

    return user;
});

export const FileUploaded = functions.region(region).storage.object().onFinalize(object => {
    const userId = (<string>object.name).split("/")[0];

    void admin.firestore().collection("users").doc(userId).get().then(doc => {
        if (doc.exists) void admin.firestore().collection("users").doc(userId).update("usedStorage", admin.firestore.FieldValue.increment(parseInt(object.size)));
        else void admin.storage().bucket().file(<string>object.name).delete();
    });

    return object;
});

export const FileDeleted = functions.region(region).storage.object().onDelete(object => {
    void admin.firestore().collection("users").doc((<string>object.name).split("/")[0]).update("usedStorage", admin.firestore.FieldValue.increment(-parseInt(object.size)));

    return object;
});

export const FileRemovedFromFirestore = functions.region(region).firestore.document("users/{userId}/files/{fileId}").onDelete((doc, context) => {
    void admin.storage().bucket().file(context.params.userId + "/" + context.params.fileId).delete();

    return doc;
});