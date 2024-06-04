
class Log {
    time: string;
    action: string;
    user: string;
    description: string;

    constructor(time: string, action: string, user: string, description: string) {
        this.time = time;
        this.action = action;
        this.user = user;
        this.description = description;
    }
    
}

export default Log;