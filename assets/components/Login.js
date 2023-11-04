import React, {useState} from "react";
import axios from "axios";
import {redirect} from "react-router-dom";

export const Login= () => {
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [error, setError] = useState("");

    const handleEmail = (e) => {
        setEmail(e.target.value);
    };

    const handlePassword = (e) => {
        setPassword(e.target.value);
    };


    const handleSubmit = async (e) =>{
        e.preventDefault();
        if(email==="" || password==="" ){
            setError("Please enter all the fields");
            return;
        }

        try {
            const response = await axios.post("/api/login", {'username': email, 'password': password});
        }
        catch(error){
            if (error.response) {
                if(error.response.status===401){
                    setError("Wrong username or password.");
                    return;
                }
                console.log(error.response);
            } else if (error.request) {
                console.log(error.request);
            } else {
                console.log('Error', error.message);
            }
        }
        redirect("/");
    }


    return(
        <div>
            <form>
                <label className="label">Email</label>
                <input onChange={handleEmail}
                       value={email} type="email" />

                <label className="label">Password</label>
                <input onChange={handlePassword}
                       value={password} type="password" />

                {error && <p>{error}</p>}
                <button onClick={handleSubmit}
                        type="submit">
                    Submit
                </button>
            </form>
        </div>
    )
}