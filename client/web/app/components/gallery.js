"use client";
import axios from "axios";
import React, { useContext, useEffect, useState } from "react";
import { GlobalContext } from "../context/GlobalContext";
import Link from "next/link";
import { RingLoader } from "react-spinners";

export const Gallery = () => {
  const { token } = useContext(GlobalContext);
  const [images, setImages] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const fetchImages = async () => {
    try {
      const resp = await axios.get(
        `${process.env.NEXT_PUBLIC_DOMAIN_NAME}/api/fetch-images`,
        {
          withCredentials: true,
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );
      console.log(resp.data.images);
      setImages(resp.data.images);
      setIsLoading(false);
    } catch (error) {
      console.log("Error fetching images:", error);
    }
  };

  useEffect(() => {
    if (token != "") {
      fetchImages();
    }
  }, [token]);
  return (
    <div className="h-screen flex flex-col items-center">
      <h2 className="text-center text-3xl p-5">Gallery</h2>
      <Link href={"/add"} className="text-center text-xl p-5">
        Add
      </Link>
      {!isLoading ? (
        <>
          <div className="images-container grid gap-10 grid-cols-4 w-full px-20">
            {images.map((image) => (
              <Link
                href={`/image/${image.id}`}
                className="image flex flex-col items-center justify-center space-y-3"
              >
                <img
                  src={`${process.env.NEXT_PUBLIC_DOMAIN_NAME}${image.image_path}`}
                  alt="not there"
                  className="w-[300px]"
                />
                <p className="text-2xl">{image.name}</p>
              </Link>
            ))}
          </div>
        </>
      ) : (
        <div className="justify-center items-center flex h-full w-full">
          <RingLoader />
        </div>
      )}
    </div>
  );
};
