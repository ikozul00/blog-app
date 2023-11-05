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
                {user && (user.role === 'admin') && <li>
                    <Link to={"/tags"}>Tags</Link>
                </li>}
                {user && <li>
                    <Link to={`/profile/${user.id}`}>Profile</Link>
                </li>}
                {user && (user.role === 'admin') &&  <li>
                    <Link to={`/profiles`}>Users</Link>
                </li>}

            </ul>
        </nav>
        <hr/>
        <Outlet/>
    </>
    )
}