import * as React from 'react';
import { useState, useEffect, useRef } from 'react';
import BaseModal from "./BaseModal";

export function AnnouncementFlashModal(props: AnnouncementFlashModalInterface) {
  const title: string = props.title
  const dataToList: Array<string> = props.errors
  const dataNumber: number = props.errorNumber
  const timer: number = props.timer;
  const setAnnouncementModals = props.setAnnouncementModals ?? null;
  
  const [modalOpacity, setModalOpacity] = useState<number>(100);
  const [modalShow, setModalShow] = useState<boolean>(props.modalShow ?? true);

  useEffect(() => {
    const interval = setInterval(() => {
      if (modalOpacity !== 0 && modalShow === true) {
        setModalOpacity(modalOpacity - 1);
      } else {
        setModalShow(false);
        clearInterval(interval)
        setAnnouncementModals([]);
      }
    }, timer);

    return () => clearInterval(interval);
  }, [modalShow, modalOpacity]);

  return (
      <React.Fragment>
        <BaseModal
              keyValue={dataNumber}
              title={title}              
              modalOpacity={modalOpacity}
              modalShow={modalShow}
              setShowModal={setModalShow}
              label={"Error announcement"}
              indexPosition={1060}
        >
          { 
            dataToList?.length > 0
            ?
              <div className="modal-body error-modal">
                {
                  dataToList.map((error, index) => (
                      <li className="error-modal-list" key={index}>{error}</li>
                  ))
                }
              </div>
            :
              null
          }
        </BaseModal>
      </React.Fragment>
  );
}

export interface AnnouncementFlashModalInterface {
  title: string; 
  errors: string[]; 
  errorNumber: number; 
  timer: number; 
  // announcementModals?: Array<typeof AnnouncementFlashModal>;
  setAnnouncementModals?: (announcementModals: Array<typeof AnnouncementFlashModal>) => void;
  modalShow?: boolean;
  setErrorCount: (errorCount: number) => void;
}
