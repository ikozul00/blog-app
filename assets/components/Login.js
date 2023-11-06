import React, {useState} from "react";
import axios from "axios";
import {useNavigate} from "react-router-dom";

export const Login= () => {
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [error, setError] = useState("");
    const [isChecked, setIsChecked] = useState("");

    const navigate = useNavigate();

    const handleEmail = (e) => {
        setEmail(e.target.value);
    };

    const handlePassword = (e) => {
        setPassword(e.target.value);
    };
    const handleCheckbox = (e) => {
        setIsChecked(e.target.value);
    };



    const handleSubmit = async (e) =>{
        e.preventDefault();
        if(email==="" || password==="" ){
            setError("Please enter all the fields");
            return;
        }

        try {
            let body={};
            if(isChecked){
                body={'username': email, 'password': password,  "_remember_me": true};
            }
            else{
                body={'username': email, 'password': password };
            }
            const response = await axios.post("/api/login", body);
            const role = response.data.role.includes('ROLE_ADMIN') ? 'admin' : 'user';
            localStorage.setItem('user',JSON.stringify({'id': response.data.id, 'role':role }));
            navigate("/");
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

    }


    return(
        <div>
            <form>
                <label className="label">Email</label>
                <input onChange={handleEmail}
                       value={email} type="email" /><br/>

                <label className="label">Password</label>
                <input onChange={handlePassword}
                       value={password} type="password" /><br/>
                <label>
                <input
                    type="checkbox"
                    checked={isChecked}
                    onChange={handleCheckbox}
                /> Remember Me
                </label><br/>
                {error && <p>{error}</p>}
                <button onClick={handleSubmit}
                        type="submit">
                    Submit
                </button>
            </form>
        </div>
    )
}