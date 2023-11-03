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
import {Posts} from "./js/Posts";
import {
    createBrowserRouter,
    RouterProvider,
} from "react-router-dom";



const App = () => {
    console.log("Component rendered");
    return(
        <div>
            <Posts/>
        </div>
    )
}

const router = createBrowserRouter([
    {
        path: "/",
        element: <App/>,
    },
]);


const root = createRoot(document.getElementById('root'));
root.render(<React.StrictMode>
    <RouterProvider router={router} />
</React.StrictMode>);
//ReactDom.render(<App />, document.getElementById('root'));


