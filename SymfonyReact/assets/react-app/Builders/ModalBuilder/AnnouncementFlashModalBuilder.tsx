import * as React from 'react';

import { AnnouncementFlashModal } from '../../Components/Modals/AnnouncementFlashModal';
import BaseModal from "../../Components/Modals/BaseModal";

export function BuildAnnouncementErrorFlashModal(
    props: {
      title: string;
      errors: string[];
      errorNumber: number;
      timer?: number|null;
    }
  ) {
  const title: string = props.title
  const errors: Array<string> = props.errors
  const errorNumber: number = props.errorNumber
  const timer: number = props.timer ?? 80;

  return (
      <React.Fragment>
        <AnnouncementFlashModal
            title={title}
            errors={errors}
            errorNumber={errorNumber}
            timer={timer}
        />
      </React.Fragment>
  );
}
