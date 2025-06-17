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
import QueryPage from "../Routes/Query/QueryPage";
import {RoomView} from "../User/Components/Room/RoomView";
import {GroupView} from "../User/Components/Group/GroupView";

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(
    <BrowserRouter basename="/HomeApp">
        <Routes>
            <Route path="/WebApp/login" element={<LoginView />}></Route>
            <Route path="/WebApp/logout" element={<Logout />}></Route>
                <Route path="/WebApp/" element={<MainPageTop  />}>
                    <Route path="index" element={<LandingPage />} />
                    <Route path="cards/index" element={<CardLandingPage />} />
                    {/*<Route path="cards/room/:entityID" element={<CardLandingPage />} />*/}
                    {/*<Route path="cards/device/:entityID" element={<CardLandingPage />} />*/}
                    <Route path="devices/:deviceID" element={<DeviceView />} />
                    <Route path="room/:roomID" element={<RoomView />} />
                    <Route path="group/:groupID" element={<GroupView />} />
                    <Route path="sensors/triggers" element={<TriggerPage />} />
                    <Route path="user-settings" element={<UserSettingsView />} />
                    <Route path="query" element={<QueryPage />} />
                </Route>
        </Routes>
    </BrowserRouter>
);
