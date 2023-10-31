import Vue from 'vue'

var languageText = window.localStorage.getItem('app-languages');

export const trans = (title_key) => {
    const langObject = JSON.parse(languageText);
    return langObject[title_key];
}
