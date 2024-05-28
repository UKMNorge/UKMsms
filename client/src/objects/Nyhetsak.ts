class Nyhetsak {
    id: string;
    navn: string;
    content: string;
    link: string;
    
    constructor(id: string, navn: string, content: string, link: string) {
        this.id = id;
        this.navn = navn;
        this.content = content;
        this.link = link;
    }

    public getId(): string {
        return this.id;
    }

    public getNavn(): string {
        return this.navn;
    }

    public getContent(): string {
        return this.content;
    }

    public getLink(): string {
        return this.link;
    }

    public getStringFormat() : String {
        return this.id + ' - ' + this.navn;
    }

    public toString() {
        return this.getStringFormat();
    }
}

export default Nyhetsak;