import { redirect } from "next/navigation";
import React from "react";
import { Gallery } from "../components/gallery";
import { cookies } from "next/headers";
import axios from "axios";

export default async function page() {
  try {
    const cookieStore = cookies();
    const authToken = cookieStore.get("at");
    const url = `${process.env.NEXT_PUBLIC_DOMAIN_NAME}/api/user-data`;
    const resp = await axios.get(url, {
      headers: { Cookie: `at=${authToken?.value}` },
    });
    axios.defaults.headers.common[
      "Authorization"
    ] = `Bearer ${resp.data.access_token}`;

    const url1 = `${process.env.NEXT_PUBLIC_DOMAIN_NAME}/api/isLog`;
    const resp1 = await axios.post(url1, {}, { withCredentials: true });
  } catch (error) {
    console.log(error);
    return redirect("/");
  }
  return (
    <div className="">
      <Gallery />
    </div>
  );
}
