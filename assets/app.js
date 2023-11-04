/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';


import React from "react";
import ReactDom from 'react-dom';
import { createRoot } from 'react-dom/client';
import {Posts} from "./components/Posts";
import {Post, loader as postLoader} from "./components/Post";
import {
    createBrowserRouter, Link,
    RouterProvider,
} from "react-router-dom";
import ErrorPage from "./components/ErrorPage";
import {Registration} from "./components/Registration";
import {Login} from "./components/Login";



const App = () => {
    console.log("Component rendered");
    return(
        <div>
            <Posts/>
            <Link to={"/registration"}>Registration</Link>
            <Link to={"/login"}>Login</Link>
        </div>
    )
}

const router = createBrowserRouter([
    {
        path: "/",
        element: <App/>,
        errorElement: <ErrorPage/>,
    },
    {
        path: "/posts/:postId",
        element: <Post/>,
        loader: postLoader,
    },
    {
        path: "/registration",
        element: <Registration/>
    },
    {
        path: "/login",
        element: <Login/>
    }

]);


const root = createRoot(document.getElementById('root'));
root.render(<React.StrictMode>
    <RouterProvider router={router} />
</React.StrictMode>);
//ReactDom.render(<App />, document.getElementById('root'));


