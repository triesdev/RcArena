import { OhVueIcon, addIcons } from "oh-vue-icons";
import {
    BiArchive,
    BiArrowLeft,
    BiBezier2,
    BiBookmarkStar,
    BiCameraVideo,
    BiCheck2All,
    BiColumnsGap,
    BiCursor,
    BiFilePost,
    BiFileEarmarkPost,
    BiGear,
    BiListCheck,
    BiMenuButtonWide,
    BiNewspaper,
    BiPeople,
    BiSearch,
    BiShieldCheck,
    BiTags,
    BiVectorPen,
    BiEye, BiWhatsapp, BiExclamation, BiExclamationCircleFill,
    BiTicketDetailed,
    BiPlus, BiTrash,
} from "oh-vue-icons/icons/bi";

addIcons(
  BiArchive,
  BiArrowLeft,
  BiBezier2,
  BiBookmarkStar,
  BiCameraVideo,
  BiCheck2All,
  BiColumnsGap,
  BiCursor,
  BiFilePost,
  BiFileEarmarkPost,
  BiGear,
  BiListCheck,
  BiMenuButtonWide,
  BiNewspaper,
  BiPeople,
  BiSearch,
  BiShieldCheck,
  BiTags,
  BiVectorPen,
  BiEye,
    BiWhatsapp,
    BiExclamationCircleFill,
    BiTicketDetailed,
    BiPlus,
    BiTrash,
);

// register components
const registerIcon = (app) => {
  app.component('v-icon', OhVueIcon);
}

export default registerIcon
