import * as React from "react";
import * as ReactDOM from "react-dom/client";
import {
    BrowserRouter,
    Routes,
    Route,
} from "react-router-dom";

import UserDataContextProvider from "../Contexts/UserData/UserDataContext";
// import '@fortawesome/fontawesome-free/js/fontawesome'
// import '@fortawesome/fontawesome-free/js/solid'
// import '@fortawesome/fontawesome-free/js/regular'
// import '@fortawesome/fontawesome-free/js/brands'

import Login from "../Routes/Login/Login";

import { LandingPage } from "../Routes/LandingPage/LandingPage";

import { MainPageTop } from "../Components/Pages/MainPageTop";

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(
    <BrowserRouter>
        <Routes>
            <Route path="/HomeApp/WebApp/login" element={<Login />}></Route>
                <Route path="/HomeApp/WebApp/" element={<MainPageTop  />}>
                    <Route path="index" element={<LandingPage />} />
                </Route>
        </Routes>
    </BrowserRouter>
);
