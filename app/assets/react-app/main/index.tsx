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


import { CardLandingPage } from "../Routes/LandingPage/CardLandingPage";


import { MainPageTop } from "../Common/Components/Pages/MainPageTop";
import { LandingPage } from '../Routes/LandingPage/LandingPage';
import { UserSettingsView } from '../User/Components/User/UserSettingsView';
import TriggerPage from '../Routes/Triggers/TriggerPage';
import { DeviceView } from "../Devices/Components/DeviceView";
import LoginView from "../Authentication/Components/LoginView";
import { Logout } from '../Authentication/Components/Logout';
import QueryView from "../Sensors/Components/QueryView";

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(
    <BrowserRouter>
        <Routes>
            <Route path="/HomeApp/WebApp/login" element={<LoginView />}></Route>
            <Route path="/HomeApp/WebApp/logout" element={<Logout />}></Route>
                <Route path="/HomeApp/WebApp/" element={<MainPageTop  />}>
                    <Route path="index" element={<LandingPage />} />
                    <Route path="cards/index" element={<CardLandingPage />} />
                    <Route path="devices/:deviceID" element={<DeviceView />} />
                    <Route path="user-settings" element={<UserSettingsView />} />
                    <Route path="sensors/triggers" element={<TriggerPage />} />
                    <Route path="query" element={<QueryView />} />
                </Route>
        </Routes>
    </BrowserRouter>
);
