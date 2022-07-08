import { webappURL } from './URLSCommon'
import { getUserID } from './Common'

function RedirectToLogin() {
    console.log(getUserID(), 'its mee');
//   window.location.replace(webappURL+'login')
}

export default RedirectToLogin;
