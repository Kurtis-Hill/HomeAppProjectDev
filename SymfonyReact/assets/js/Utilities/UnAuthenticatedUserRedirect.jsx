import { webappURL } from '../Utilities/URLSCommon'
import { getUserID } from '../Utilities/Common'

function RedirectToLogin() {
    console.log(getUserID(), 'its mee');
//   window.location.replace(webappURL+'login')
}

export default RedirectToLogin;