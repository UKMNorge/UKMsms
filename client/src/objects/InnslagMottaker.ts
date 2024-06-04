class InnslagMottaker {
    public navn: String;
    public mobil: String;
    public innslagNavn : String;
    public innslagType : String;

    // Make konstruktør
    constructor(navn : String, mobil : String, innslagNavn : String, innslagType : String) {
        this.navn = navn;
        this.mobil = mobil;
        this.innslagNavn = innslagNavn;
        this.innslagType = innslagType;
    }

    getNavn() : String {
        return this.navn;
    }

    getMobil() : String {
        return this.mobil;
    }

    getInnslagNavn() : String {
        return this.innslagNavn;
    }

    getInnslagType() : String {
        return this.innslagType;
    }

    public isMobilValid() : boolean {
        const mobileNumberPattern = /^\d{1,8}$/;
        return mobileNumberPattern.test(this.mobil.toString());
    }

    public toString() : String {
        return this.navn + ' - ' + this.mobil;
    }
}

export default InnslagMottaker;
