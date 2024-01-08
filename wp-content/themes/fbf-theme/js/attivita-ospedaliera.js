const servizioAfferente = document.querySelectorAll(".servizio-afferente");

const handleClickServizioAfferente = ({
  currentTarget: servizioSelezionato,
}) => {
  if (document.querySelector(".servizio-selezionato")) {
    resetSelezioneServiziAfferenti();
    document
      .querySelector(".servizio-selezionato")
      .classList.remove("servizio-selezionato");
  }
  servizioSelezionato.classList.add("servizio-selezionato");
  const idServizioSelezionato = servizioSelezionato.id;
  mostraRiquadro("servizio" + idServizioSelezionato);
};

Array.from(servizioAfferente).forEach((servizio) => {
  servizio.addEventListener("click", handleClickServizioAfferente);
});

const resetSelezioneServiziAfferenti = () => {
  const riquadriDettaglioServizi = document.querySelectorAll(
    ".dettaglio-servizio-afferente"
  );
  for (let riquadro of riquadriDettaglioServizi) {
    riquadro.style.display = "none";
  }
};

const mostraRiquadro = (id) => {
  const riquadroDaMostrare = document.getElementById(id);
  riquadroDaMostrare.style.display = "block";
};
const handleClickChiudiServizioAfferente = () => {
  resetSelezioneServiziAfferenti();
  if (document.querySelector(".servizio-selezionato")) {
    document
      .querySelector(".servizio-selezionato")
      .classList.remove("servizio-selezionato");
  }
};

const pulsantiChiudiServizioAfferente = document.querySelectorAll(
  ".chiudi-servizio-afferente"
);
Array.from(pulsantiChiudiServizioAfferente).forEach((pulsante) => {
  pulsante.addEventListener("click", handleClickChiudiServizioAfferente);
});
