import { storage } from "./firebase";
import { ref, getDownloadURL, uploadBytes } from "firebase/storage";
import moment from "moment";
import { makeToken } from "./helper";

async function handleFileProcessData(file) {
  // const file = this.$refs.myfile.files[0];
  // Generate new file name
  const newFileName = generateFileName(file);
  return await upload(newFileName, file);
}

async function upload(fileName, file) {
  const path = "RcArenaEvent/" + fileName;
  const storageRef = ref(storage, path);
  await uploadBytes(storageRef, file);
  return await getDownloadURL(ref(storage, path))
}

function generateFileName(file) {
  let name = moment().format("YYYYMMDD");
  name += makeToken(30);
  const fileExtension = file.name.slice(
    ((file.name.lastIndexOf(".") - 1) >>> 0) + 2
  );

  return `${name}.${fileExtension}`;
}

export { handleFileProcessData };