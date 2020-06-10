import React, { useState, useEffect, useRef } from 'react';

import axios from 'axios';

const cardFormModal = (cardViewid, room, sensorname) => {

  console.log("pressed" + cardViewid);

  const [modalShow, setModalShow] = useState ('false');

  axios.get('/HomeApp/api/CardData/cardviewform&id='+cardViewid,
  { headers: {"Authorization" : `Bearer ${token}`} })
  .then(response => {
      const usecardFormModal = cardFormModal(cardViewid, sensorname, response.data);
      // this.setState({modalLoading: false});
      // this.setState({modalContent: usecardFormModal});
      // // this.modalContent(response.data, id);
      // this.setState({modalShow: true});
      console.log(response.data);
      
  }).catch(error => {
      console.log(error);
  })
 
  const formURL = 'api/CardData/cardviewform&id='+cardViewid;
  console.log("response", cardData);
  const userData = cardData[0];

  if (userData.t_tempid != null) {
      var sensorHighReading = userData.t_hightemp;
      var sensorLowReadings = userData.t_lowtemp;
      var constRecord = userData.t_constrecord ? "selected" : null;
      var sensorID = userData.t_tempid;

      if (userData.h_humidid !== undefined) {
          var humidHighReading = userData.h_highhumid;
          var sensorLowReading = userData.h_lowhumid;
          var sensorTwoID = userData.h_humidid;
      }
  }

  if (userData.a_analogid != null) {
      var sensorHighReading = userData.h_highanalog;
      var sensorLowReadings = userData.h_lowanalog;
      var sensorID = userData.h_analogid;
  }

  const capitalizeFirstLetter =  (string) => {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  const sensorName = userData.sensorname;

  const currentIcon = userData.i_iconname;
  const icons = cardData[1];

  const currentColour = capitalizeFirstLetter(userData.cc_colour);
  const colours = cardData[2];

  const currentCardView = capitalizeFirstLetter(userData.cs_state);
  const states = cardData[3];

  const modalShow = true;

  const modalStyle = modalShow !== true ? {paddingRight: '17px', display: 'block'} : {display: 'none'};

  const token = sessionStorage.getItem('token');

  const toggleModal = () => {

  }
  
  return ( 

    
    <div id="" style={{paddingRight: '17px', display: 'block'}} className="modal-show modal fade show"  tabIndex={-1} role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div className="modal-dialog" role="document">
      <div className="modal-content">
       
      </div>
    </div>
  </div>
    // <form method="post" action={formURL} >
    //   <div className="modal-header">
    //       <h5 className="modal-title" id="exampleModalLabel">Change {sensorName}'s Sensor Details</h5>
    //           <button className="close" onClick={() => {this.toggleModal()}} type="button" data-dismiss="modal" aria-label="Close">
    //               <span aria-hidden="true">×</span>
    //               <span onClick={toggleModal()} aria-hidden="true">×</span>
    //           </button>
    //   </div>
    //   <div className="modal-body">
    //       <label className="modal-element large font-weight-bold">High Reading</label>
    //       <input type="text" name="highReading" className="form-control" placeholder={sensorHighReading}></input>

    //       <label className="modal-element large font-weight-bold">Low Reading</label>
    //       <input type="text" name="lowReading" className="form-control" placeholder={sensorLowReadings}></input>

    //       <label className="modal-element large font-weight-bold">Icon</label>
    //       <br />
    //       <select name="cardiconid" defaultValue={capitalizeFirstLetter(currentIcon)} className="form-space">
    //           {icons.map((icons, index) => (
    //               <option value={icons.i_iconid} key={icons.i_iconid}>{capitalizeFirstLetter(icons.i_iconname)}</option>
    //               ))}
    //       </select>
    //       <i className={"fas fa-2x text-gray-300 modal-icon fa-"+currentIcon}></i>
    //       <br />

    //       <label className="modal-element large font-weight-bold">Card Colour</label>
    //       <select name="cardcolourid" defaultValue={currentColour} className="form-control">
    //           {colours.map((colours, index) => (
    //           <option value={colours.c_colourid} key={colours.c_colourid}>{colours.c_shade}</option>
    //           ))}
    //       </select>

    //       <label className="modal-element large font-weight-bold">Card View</label>
    //       <select name="cardSensorStateOne" defaultValue={currentCardView} className="form-control">
    //           {states.map((states, index) => (
    //           <option value={states.cs_cardstateid} key={states.cs_cardstateid}>{capitalizeFirstLetter(states.cs_state)}</option>
    //           ))}
    //       </select>

    //       <label className="modal-element large font-weight-bold">Constantly Record Data</label>
    //       <select className="form-control">
    //           <option value="0" key="no" selected={constRecord}>No</option>
    //           <option value="1" key="yes">Yes</option>
    //       </select>
    //   </div>
    //   <div className="modal-footer">
    //       <button className="btn btn-secondary" type="button" onClick={() => {toggleModal()}} data-dismiss="modal">Cancel</button>
    //       <button className="btn btn-primary" name="submit" action="submit">Submit</button>
    //   </div>
    // </form> 
  )
}

export default cardFormModal;