const attivaRicerca = () => {
  const casellaDiRicerca = document.getElementById("s");
  setTimeout(() => casellaDiRicerca.focus(), 1000);
};

const pulsanteRicerca = document.getElementById("ricercaNelSito");

pulsanteRicerca.addEventListener("click", () => {
  attivaRicerca();
});
