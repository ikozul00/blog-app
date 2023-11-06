import React, { useEffect, useState} from "react";
import {Link, useLoaderData, useParams} from "react-router-dom";
import {useNavigate} from "react-router-dom";
import axios from "axios";
import {formatDate} from "./helperFunctions";

const numberOfItemsPerPage = 10;

export const loader = async ({params}) => {
    const language = params.lang ? params.lang : 'en';
    const labels=await axios.get(`/api/${language}/posts-page`);
    if(labels.status !== 200){
        return{error: "Error while fetching post data."}
    }
    return labels.data;
}

export const Posts = ({}) => {
    const [posts, setPosts] = useState([]);
    const [page, setPage] = useState(1);
    const [filter, setFilter] = useState("");
    const [isLoadMore, setIsLoadMore] = useState(true);

    const user = JSON.parse(localStorage.getItem('user'));

    const navigation = useNavigate();
    const {title, add, more, search} = useLoaderData();

    const params = useParams();
    const language = params.lang ? params.lang : 'en';


    useEffect( () => {
        const getPosts = async () => {
            try{
                const response = await axios.get(`/api/posts/?page=${page}&&limit=${numberOfItemsPerPage}`);
                const {postsList, postsCount, currentPageNumber} = response.data;
                setPosts([...posts, ...postsList ]);
                if(currentPageNumber*numberOfItemsPerPage < postsCount){
                    setPage(currentPageNumber+1);
                }
                else{
                    setIsLoadMore(false);
                }
            }
            catch (error){
                console.log(error);
            }
        }
        getPosts();
    }, []);

    const loadMorePosts = async (initialPosts= posts, initialPage = page) => {
        try {
            const requestUrl = filter !== "" ?
                `/api/posts/?page=${initialPage}&&limit=${numberOfItemsPerPage}&&filter=${filter}` :
                `/api/posts/?page=${initialPage}&&limit=${numberOfItemsPerPage}`;
            const response = await axios.get(requestUrl);
            const {postsList, postsCount, currentPageNumber} = response.data;
            setPosts([...initialPosts, ...postsList]);
            if (currentPageNumber * numberOfItemsPerPage < postsCount) {
                setPage(currentPageNumber + 1);
                setIsLoadMore(true);
            } else {
                setIsLoadMore(false);
            }
        }
        catch(error){
            console.log(error);
        }
    }


    const handleAddPost = () => {
        navigation("/posts/add")
    }

    const handleSearch = () => {
        loadMorePosts([], 1);
    }

    const handleLanguageChange = (lang) =>{
        navigation(`/${lang}`);
    }

    return (
        <>
            <button onClick={() => handleLanguageChange('hr')}>Hrvatski</button>
            <button onClick={() => handleLanguageChange('en')}>English</button>
            <h1>{title}</h1>
            <input type={"text"} value={filter} name={"filter"} id={"filter"}
                   onChange={(e) => setFilter(e.target.value)}/>
            <button onClick={handleSearch}>{search}</button>
            <div>
            {posts.map(post =>
            {
                return(
                <div key={post.id}>
                    <Link to={`/post/${language}/${post.id}`} >{post.title}</Link>
                    <span>{formatDate(post.createdAt.date, language)}</span>
                </div>
            )})}
            </div>
            {isLoadMore && <button onClick={() => loadMorePosts()}>{more}</button>}<br/>
            {user && user.role==="admin" && <button onClick={handleAddPost}>{add}</button>}
        </>
    )
}
