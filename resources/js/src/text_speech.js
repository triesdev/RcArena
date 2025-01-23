function speechText(text = 'speaker active'){
    const speech = new SpeechSynthesisUtterance(text);
    speech.pitch = 1;
    speech.rate = 0.9;
    speech.voice = window.speechSynthesis.getVoices().find(voz => voz.lang === "id-ID");
    setTimeout(function(){
        window.speechSynthesis.speak(speech);
    }, 1000);
}

export {speechText}
