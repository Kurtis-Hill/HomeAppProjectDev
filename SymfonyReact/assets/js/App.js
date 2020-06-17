import React, { Component } from "react";
import ReactDOM from "react-dom";
import { BrowserRouter as Router, Route, Switch, NavLink} from 'react-router-dom';
//may not need react-router DEFFIANTLY NEED react-router-dom


import Navbar from "./components/Navbar";
import Cards from "./components/Cards";
import  CardModal  from './components/CardFormModal.jsx';

import CardContextProvider from "./contexts/CardContexts";
import NavbarContextProvider from "./contexts/NavbarContext";
import Profilebar from "./components/Profilebar";

import Login from './components/Login';

import { getToken } from './Utilities/Common';

import axios from 'axios';


export default class App extends Component {

       
    
    render() {
        return (    
            
        <Router>
            <Route path="/HomeApp/login" component={Login}>
            </Route>
            <div id="page-top">
                <div id="wrapper">
                    <Route path="/HomeApp/index">
                       
                        <div className="d-sm-flex align-items-center justify-content-between mb-4">
                        </div>
                            <NavbarContextProvider>
                                <Navbar/>
                            </NavbarContextProvider>
                            <div id="content-wrapper" className="d-flex flex-column">
                                <div id="content">
                                    <Profilebar></Profilebar>
                                    <CardContextProvider>
                                        <Cards/>                                        
                                    </CardContextProvider>
                                </div>
                            </div>
                    </Route>
                </div>
            </div>
        </Router>  

      
        );
    }
}

ReactDOM.render(<App/>, document.getElementById("root"));