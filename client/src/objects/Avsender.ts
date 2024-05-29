class Avsender {
    public navn: String;
    public telefonnummer: String;

    // Make konstruktør
    constructor(navn : String, telefonnummer : String) {
        this.navn = navn;
        this.telefonnummer = telefonnummer;
    }

    getNavn() {
        return this.navn;
    }

    getTelefonnummer() {
        return this.telefonnummer;
    }

    public isTelefonnummerValid() : boolean {
        const mobileNumberPattern = /^\d{1,8}$/;
        return mobileNumberPattern.test(this.telefonnummer.toString());
    }

    public toString() : String {
        return this.navn + ' - ' + this.telefonnummer;
    }
}


export default Avsender;
