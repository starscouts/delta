const fs = require('fs');
const crypto = require('crypto');

if (!fs.existsSync("../data/translations")) fs.mkdirSync("../data/translations");

let articles = fs.readdirSync("../data/articles");
let albums = fs.readdirSync("../data/gallery");
let people = fs.readdirSync("../data/people");
let profiles = fs.readdirSync("../data/profiles");

(async () => {
    for (let article of articles) {
        let data = JSON.parse(fs.readFileSync("../data/articles/" + article).toString());
        let text = data['contents'];

        let res = {
            md5: crypto.createHash("md5").update(fs.readFileSync("../data/articles/" + article).toString()).digest("hex"),
            date: new Date().getTime(),
            translations: {}
        }

        if (text && text.trim() !== "") {
            res["translations"][data['language'] === "en" ? "fr" : "en"] = {};
            res["translations"][data['language'] === "en" ? "fr" : "en"]["contents"] = (await require('./html')("<div>" + text.replace(/<(img|col|br)( ([^>]*)|)>/gm, "<$1$2 />").replace(/&(?![a-z]+;)/gm, "&amp;") + "</div>", data['language'], data['language'] === "en" ? "fr" : "en")).replace(/<([^-]+)---[a-f\d]{8}(( [^>]+)|)>/gm, "<$1$2>");
        }

        fs.writeFileSync("../data/translations/" + article, JSON.stringify(res));
    }

    for (let article of albums) {
        let data = JSON.parse(fs.readFileSync("../data/albums/" + article).toString());
        let text = data['contents'];

        let res = {
            md5: crypto.createHash("md5").update(fs.readFileSync("../data/albums/" + article).toString()).digest("hex"),
            date: new Date().getTime(),
            translations: {}
        }

        if (text && text.trim() !== "") {
            res["translations"][data['language'] === "en" ? "fr" : "en"] = {};
            res["translations"][data['language'] === "en" ? "fr" : "en"]["contents"] = (await require('./html')("<div>" + text.replace(/<(img|col|br)( ([^>]*)|)>/gm, "<$1$2 />").replace(/&(?![a-z]+;)/gm, "&amp;") + "</div>", data['language'], data['language'] === "en" ? "fr" : "en")).replace(/<([^-]+)---[a-f\d]{8}(( [^>]+)|)>/gm, "<$1$2>");
        }

        fs.writeFileSync("../data/translations/" + article, JSON.stringify(res));
    }

    for (let article of profiles) {
        let data = JSON.parse(fs.readFileSync("../data/profiles/" + article).toString());
        let text = data['contents'];

        let res = {
            md5: crypto.createHash("md5").update(fs.readFileSync("../data/profiles/" + article).toString()).digest("hex"),
            date: new Date().getTime(),
            translations: {}
        }

        if (text && text.trim() !== "") {
            res["translations"][data['language'] === "en" ? "fr" : "en"] = {};
            res["translations"][data['language'] === "en" ? "fr" : "en"]["contents"] = (await require('./html')("<div>" + text.replace(/<(img|col|br)( ([^>]*)|)>/gm, "<$1$2 />").replace(/&(?![a-z]+;)/gm, "&amp;") + "</div>", data['language'], data['language'] === "en" ? "fr" : "en")).replace(/<([^-]+)---[a-f\d]{8}(( [^>]+)|)>/gm, "<$1$2>");
        }

        fs.writeFileSync("../data/translations/" + article, JSON.stringify(res));
    }

    for (let article of people) {
        let data = JSON.parse(fs.readFileSync("../data/people/" + article).toString());

        let res = {
            md5: crypto.createHash("md5").update(fs.readFileSync("../data/people/" + article).toString()).digest("hex"),
            date: new Date().getTime(),
            translations: {}
        }

        if (data['contents'] && data['contents'].trim() !== "") {
            if (!res["translations"][data['language'] === "en" ? "fr" : "en"]) res["translations"][data['language'] === "en" ? "fr" : "en"] = {};
            res["translations"][data['language'] === "en" ? "fr" : "en"]["contents"] = (await require('./html')("<div>" + data['contents'].replace(/<(img|col|br)( ([^>]*)|)>/gm, "<$1$2 />").replace(/&(?![a-z]+;)/gm, "&amp;") + "</div>", data['language'], data['language'] === "en" ? "fr" : "en")).replace(/<([^-]+)---[a-f\d]{8}(( [^>]+)|)>/gm, "<$1$2>");
        }

        if (data['diplomas'] && data['diplomas'].length > 0) {
            if (!res["translations"][data['language'] === "en" ? "fr" : "en"]) res["translations"][data['language'] === "en" ? "fr" : "en"] = {};
            if (!res["translations"][data['language'] === "en" ? "fr" : "en"]['diplomas']) res["translations"][data['language'] === "en" ? "fr" : "en"]['diplomas'] = [];

            for (let diploma of data['diplomas']) {
                res["translations"][data['language'] === "en" ? "fr" : "en"]["diplomas"].push(await require('./translate')(diploma, data['language'], data['language'] === "en" ? "fr" : "en"));
            }
        }

        if (data['jobs'] && data['jobs']['current'] && data['jobs']['current'].trim() !== "") {
            if (!res["translations"][data['language'] === "en" ? "fr" : "en"]) res["translations"][data['language'] === "en" ? "fr" : "en"] = {};
            if (!res["translations"][data['language'] === "en" ? "fr" : "en"]['jobs']) res["translations"][data['language'] === "en" ? "fr" : "en"]['jobs'] = {};
            res["translations"][data['language'] === "en" ? "fr" : "en"]["jobs"]["current"] = await require('./translate')(data['jobs']['current'], data['language'], data['language'] === "en" ? "fr" : "en");
        }

        if (data['jobs'] && data['jobs']['position'] && data['jobs']['position'].trim() !== "") {
            if (!res["translations"][data['language'] === "en" ? "fr" : "en"]) res["translations"][data['language'] === "en" ? "fr" : "en"] = {};
            if (!res["translations"][data['language'] === "en" ? "fr" : "en"]['jobs']) res["translations"][data['language'] === "en" ? "fr" : "en"]['jobs'] = {};
            res["translations"][data['language'] === "en" ? "fr" : "en"]["jobs"]["position"] = await require('./translate')(data['jobs']['position'], data['language'], data['language'] === "en" ? "fr" : "en");
        }

        if (data['jobs'] && data['jobs']['place'] && data['jobs']['place'].trim() !== "") {
            if (!res["translations"][data['language'] === "en" ? "fr" : "en"]) res["translations"][data['language'] === "en" ? "fr" : "en"] = {};
            if (!res["translations"][data['language'] === "en" ? "fr" : "en"]['jobs']) res["translations"][data['language'] === "en" ? "fr" : "en"]['jobs'] = {};
            res["translations"][data['language'] === "en" ? "fr" : "en"]["jobs"]["place"] = await require('./translate')(data['jobs']['place'], data['language'], data['language'] === "en" ? "fr" : "en");
        }

        if (data['jobs'] && data['jobs']['next'] && data['jobs']['next'].trim() !== "") {
            if (!res["translations"][data['language'] === "en" ? "fr" : "en"]) res["translations"][data['language'] === "en" ? "fr" : "en"] = {};
            if (!res["translations"][data['language'] === "en" ? "fr" : "en"]['jobs']) res["translations"][data['language'] === "en" ? "fr" : "en"]['jobs'] = {};
            res["translations"][data['language'] === "en" ? "fr" : "en"]["jobs"]["next"] = await require('./translate')(data['jobs']['next'], data['language'], data['language'] === "en" ? "fr" : "en");
        }

        fs.writeFileSync("../data/translations/" + article, JSON.stringify(res));
    }
})()