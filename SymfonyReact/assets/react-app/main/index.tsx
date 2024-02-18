import * as React from "react";
import * as ReactDOM from "react-dom/client";
import {
    BrowserRouter,
    Routes,
    Route,
} from "react-router-dom";

import UserDataContextProvider from "../User/Contexts/UserDataContext";
// import '@fortawesome/fontawesome-free/js/fontawesome'
// import '@fortawesome/fontawesome-free/js/solid'
// import '@fortawesome/fontawesome-free/js/regular'
// import '@fortawesome/fontawesome-free/js/brands'

import Login from "../Routes/Login/Login";

import { CardLandingPage } from "../Routes/LandingPage/CardLandingPage";


import { MainPageTop } from "../Common/Components/Pages/MainPageTop";
import { LandingPage } from '../Routes/LandingPage/LandingPage';
import { DevicePage } from '../Devices/Page/DevicePage';
import { UserSettingsPage } from '../User/Pages/UserSettingsPage';
import { Logout } from '../Routes/Logout/Logout';

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(
    <BrowserRouter>
        <Routes>
            <Route path="/HomeApp/WebApp/login" element={<Login />}></Route>
            <Route path="/HomeApp/WebApp/logout" element={<Logout />}></Route>
                <Route path="/HomeApp/WebApp/" element={<MainPageTop  />}>
                    <Route path="index" element={<LandingPage />} />
                    <Route path="cards/index" element={<CardLandingPage />} />
                    <Route path="devices/:deviceID" element={<DevicePage />} />
                    <Route path="user-settings" element={<UserSettingsPage />} />
                    {/*<Route path="cards/device/{id}" element={<CardLandingPage />} />*/}
                </Route>
        </Routes>
    </BrowserRouter>
);
