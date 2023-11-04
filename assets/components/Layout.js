import React from "react";
import {Link, Outlet} from "react-router-dom";

export const Layout = () => {
    const user = JSON.parse(localStorage.getItem('user'));

    return(
    <>
        <h1>Blog App</h1>
        <nav>
            <ul>
                <li>
                    <Link to={"/"}>Posts</Link>
                </li>
                <li>
                    <Link to={"/login"}>Login</Link>
                </li>
                <li>
                    <Link to={"/registration"}>Registration</Link>
                </li>
                {user && <li>
                    <Link to={"/profile"}>Profile</Link>
                </li>}

            </ul>
        </nav>
        <hr/>
        <Outlet/>
    </>
    )
}