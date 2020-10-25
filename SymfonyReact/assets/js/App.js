import React, { Component } from "react";
import ReactDOM from "react-dom";
import { BrowserRouter as Router, Route, Switch, NavLink} from 'react-router-dom';

import { removeUserSession } from "./Utilities/Common";

import Navbar from "./components/Navbar";
import Cards from "./components/Cards";
import CardModal from "./components/CardModal";


import CardContextProvider from "./contexts/CardContexts";
import NavbarContextProvider from "./contexts/NavbarContext";
import AddNewDeviceContextProvider from "./contexts/AddNewDeviceContext";
import Profilebar from "./components/Profilebar";

import Login from './components/Login';
import AddNewSensor from './components/AddNewSensor';
import AddNewDevice from './components/AddNewDevice';

export default class App extends Component {
    render() {
        return (                
        <Router>
            <Switch>
                <Route exact path="/HomeApp/login" component={Login}/>
                <Route exact path="/HomeApp/register"/>
                <Route exact path="/HomeApp/logout" component={() => removeUserSession()}/>
                <React.Fragment>
                    <div id="page-top">
                        <div id="wrapper">                     
                            <NavbarContextProvider>                                
                                <Navbar/>
                                <AddNewDeviceContextProvider>
                                    <AddNewDevice/>
                                </AddNewDeviceContextProvider>
                            </NavbarContextProvider>                                               
                            <div id="content-wrapper" className="d-flex flex-column">
                                <div id="content">  
                                    <Profilebar></Profilebar>
                                        <Route path="/HomeApp/WebApp/index">                                                                                                                                         
                                            <CardContextProvider>
                                                <Cards/>       
                                                <CardModal/>                                 
                                            </CardContextProvider>                                        
                                        </Route>                                   
                                        <Route path="/HomeApp/WebApp/device">                                      
                                            <CardContextProvider>
                                                <Cards/>     
                                                <CardModal/>                                     
                                            </CardContextProvider>
                                            <AddNewSensor/>
                                        </Route>
                                </div>
                            </div>         
                        </div>
                    </div>
                </React.Fragment>
            </Switch>
        </Router>        
        );
    }
}
                                            

ReactDOM.render(<App/>, document.getElementById("root"));


{/* <div className="d-sm-flex align-items-center justify-content-between mb-4">
</div>      */}