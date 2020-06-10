import React, { useState, useEffect, useRef } from 'react';

import axios from 'axios';

function cardFormModal(id, room, sensorname) {
  console.log('hgey');
    // const context = useContext(CardContext);

    // const [updateCard, setupdateCard] = useState('');
    const modalShow = true;
     const modalStyle = modalShow !== true ? {paddingRight: '17px', display: 'block'} : {display: 'none'};

    const token = sessionStorage.getItem('token');
    
    const getCardFormData = () => {
      console.log('hey');
      // axios.get('/HomeApp/api/CardData/cardviewform&id='+id,
      // { headers: {"Authorization" : `Bearer ${token}`} })
      // .then(response => {
      //     // this.setState({modalLoading: false});
      //     // this.modalContent(response.data, id);
      //     // this.setState({modalShow: true});
      //     // console.log(response.data);
      // }).catch(error => {
      //     console.log(error);
      // })
    }

    const modalContent = (response, id) => {


    }

    return ( 
      <div id="" style={modalStyle} className="modal-show modal fade show"  tabIndex={-1} role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div className="modal-dialog" role="document">
          <div className="modal-content">

            {/* <form method="post" action={formURL} >
              <div className="modal-header">
                  <h5 className="modal-title" id="exampleModalLabel">Change {sensorName}'s Sensor Details</h5>
                      <button className="close" onClick={() => {this.toggleModal()}} type="button" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">×</span>
                          <span onClick={context.toggleModal()} aria-hidden="true">×</span>
                      </button>
              </div>
              <div className="modal-body">
                  <label className="modal-element large font-weight-bold">High Reading</label>
                  <input type="text" name="highReading" className="form-control" placeholder={sensorHighReading}></input>

                  <label className="modal-element large font-weight-bold">Low Reading</label>
                  <input type="text" name="lowReading" className="form-control" placeholder={sensorLowReadings}></input>

                  <label className="modal-element large font-weight-bold">Icon</label>
                  <br />
                  <select name="cardiconid" defaultValue={this.capitalizeFirstLetter(currentIcon)} className="form-space">
                      {icons.map((icons, index) => (
                          <option value={icons.i_iconid} key={icons.i_iconid}>{this.capitalizeFirstLetter(icons.i_iconname)}</option>
                          ))}
                  </select>
                  <i className={"fas fa-2x text-gray-300 modal-icon fa-"+currentIcon}></i>
                  <br />

                  <label className="modal-element large font-weight-bold">Card Colour</label>
                  <select name="cardcolourid" defaultValue={currentColour} className="form-control">
                      {colours.map((colours, index) => (
                      <option value={colours.c_colourid} key={colours.c_colourid}>{colours.c_shade}</option>
                      ))}
                  </select>

                  <label className="modal-element large font-weight-bold">Card View</label>
                  <select name="cardSensorStateOne" defaultValue={currentCardView} className="form-control">
                      {states.map((states, index) => (
                      <option value={states.cs_cardstateid} key={states.cs_cardstateid}>{this.capitalizeFirstLetter(states.cs_state)}</option>
                      ))}
                  </select>

                  <label className="modal-element large font-weight-bold">Constantly Record Data</label>
                  <select className="form-control">
                      <option value="0" key="no" selected={constRecord}>No</option>
                      <option value="1" key="yes">Yes</option>
                  </select>
              </div>
              <div className="modal-footer">
                  <button className="btn btn-secondary" type="button" onClick={() => {context.toggleModal()}} data-dismiss="modal">Cancel</button>
                  <button className="btn btn-primary" name="submit" action="submit">Submit</button>
              </div>
            </form> */}

          </div>
        </div>
      </div>
    )

}

export default cardFormModal;