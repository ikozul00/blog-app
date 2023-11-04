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
import { createRoot } from 'react-dom/client';
import {Posts} from "./components/Posts";
import {Post, loader as postLoader} from "./components/Post";
import {
    createBrowserRouter,
    RouterProvider,
} from "react-router-dom";
import ErrorPage from "./components/ErrorPage";
import {Registration} from "./components/Registration";
import {Login} from "./components/Login";
import {Layout} from "./components/Layout";
import {Profile} from "./components/Profile";
import {NewPost} from "./components/NewPost";
import {UpdatePost} from "./components/UpdatePost";
import {Tags, loader as tagsLoader} from "./components/Tags";


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
        element: <Layout/>,
        errorElement: <ErrorPage/>,
        children:[
            {
                path: "/",
                element: <App/>,
            },
            {
                path: "/posts/:postId",
                element: <Post/>,
                loader: postLoader,
            },
            {
                path: "/posts/addPost",
                element: <NewPost/>,
            },
            {
                path: "/posts/updatePost/:postId",
                element: <UpdatePost/>,
                loader: postLoader,
            },
            {
                path: "/registration",
                element: <Registration/>
            },
            {
                path: "/login",
                element: <Login/>
            },
            {
                path: "/tags",
                element: <Tags/>,
                loader: tagsLoader,
            },
            {
                path: "/profile",
                element: <Profile/>
            }
        ]
    },



]);


const root = createRoot(document.getElementById('root'));
root.render(<React.StrictMode>
    <RouterProvider router={router} />
</React.StrictMode>);
//ReactDom.render(<App />, document.getElementById('root'));


