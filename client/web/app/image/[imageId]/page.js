import EditButton from "@/app/components/editButton";
import axios from "axios";
import { cookies } from "next/headers";
import Link from "next/link";
import React from "react";

export default async function page({ params }) {
  const { imageId } = params;
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
  const resp = await axios.get(
    `${process.env.NEXT_PUBLIC_DOMAIN_NAME}/api/image-details/${imageId}`,
    { withCredentials: true }
  );
  console.log(resp.data);
  return (
    <div className="h-screen flex flex-col items-center space-y-6">
      <div className="text-black text-center p-5 text-3xl capitalize">
        {resp.data.image.image_name}
      </div>
      <div className="flex space-x-4">
        <Link href={"/main"}>View All</Link>
        <EditButton
          originalObject={resp.data.image.object_id}
          originalModel={resp.data.image.model}
          originalSpecification={resp.data.image.specification}
          originalAttachment={resp.data.image.attachment}
          originalManufacturer={resp.data.image.manufacturer}
          originalName={resp.data.image.image_name}
          imageId={imageId}
        />
      </div>
      <img
        src={`${process.env.NEXT_PUBLIC_DOMAIN_NAME}${resp.data.image.image_path}`}
        className="w-[1000px]"
        alt=""
      />
      <div className="info flex justify-around w-[1000px]">
        <div className="object flex flex-col space-y-1">
          <h2 className="text-center text-2xl">Object</h2>
          <p className="text-center">{resp.data.image.object_name}</p>
        </div>
        <div className="equipment flex flex-col space-y-1 text-center">
          <h2 className="text-center text-2xl">Equipment</h2>
          <p>Manufacturer: {resp.data.image.manufacturer}</p>
          <p>Model: {resp.data.image.model}</p>
          <p>Specification: {resp.data.image.specification}</p>
          <p>Attachment: {resp.data.image.attachment}</p>
        </div>
      </div>
    </div>
  );
}
