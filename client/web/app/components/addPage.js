"use client";
import axios from "axios";
import Link from "next/link";
import React, { useEffect, useState } from "react";

export const AddPage = () => {
  const [name, setName] = useState("");
  const [manufacturer, setManufacturer] = useState("");
  const [model, setModel] = useState("");
  const [specification, setSpecification] = useState("");
  const [attachment, setAttachment] = useState("");
  const [image, setImage] = useState(null);
  const [technique, setTechnique] = useState("");
  const [value, setValue] = useState("");
  const [isTechniqueFilled, setIsTechniqueFilled] = useState(false);
  const [categories, setCategories] = useState([]);
  const [object, setObject] = useState(1);

  const getCategories = async () => {
    const resp = await axios.get(
      `${process.env.NEXT_PUBLIC_DOMAIN_NAME}/api/categories`
    );
    console.log(resp.data.categories);
    setCategories(resp.data.categories);
  };
  useEffect(() => {
    getCategories();
  }, []);

  const handleSubmit = async (event) => {
    try {
      event.preventDefault();
      // Validation
      if (!name || !manufacturer || !model || !specification || !attachment) {
        alert("Please fill in all required fields.");
        return;
      }
      if (technique && !value) {
        alert("Please fill in the value.");
        return;
      }
      // Submission logic
      const formData = new FormData();
      formData.append("name", name);
      formData.append("manufacturer", manufacturer);
      formData.append("model", model);
      formData.append("specification", specification);
      formData.append("attachment", attachment);
      formData.append("image", image);
      formData.append("object", object);
      formData.append("technique", technique || "");
      formData.append("value", value || "");
      console.log("Submitted data:", formData);
      const resp = await axios.post(
        `${process.env.NEXT_PUBLIC_DOMAIN_NAME}/api/upload`,
        formData,
        { withCredentials: true }
      );
      resetFields();
    } catch (err) {
      console.log(err);
    }
  };

  const resetFields = () => {
    setName("");
    setManufacturer("");
    setModel("");
    setSpecification("");
    setAttachment("");
    setImage(null);
    setTechnique("");
    setValue("");
    setIsTechniqueFilled(false);
  };

  return (
    <div className="bg-white h-screen flex space-y-5 flex-col justify-center items-center">
      <Link className="text-xl text-center" href={"/main"}>
        View all
      </Link>
      <form className="form flex flex-col space-y-3" onSubmit={handleSubmit}>
        <label htmlFor="name">Name</label>
        <input
          id="name"
          className="border border-black"
          type="text"
          value={name}
          onChange={(e) => setName(e.target.value)}
          required
        />
        <h1 className="text-2xl">Equipment</h1>
        <label htmlFor="manufacturer">Manufacturer</label>
        <input
          id="manufacturer"
          className="border border-black"
          type="text"
          value={manufacturer}
          onChange={(e) => setManufacturer(e.target.value)}
          required
        />
        <label htmlFor="model">Model</label>
        <input
          id="model"
          className="border border-black"
          type="text"
          value={model}
          onChange={(e) => setModel(e.target.value)}
          required
        />
        <label htmlFor="specification">Specification</label>
        <input
          id="specification"
          className="border border-black"
          type="text"
          value={specification}
          onChange={(e) => setSpecification(e.target.value)}
          required
        />
        <label htmlFor="attachment">Attachment</label>
        <input
          id="attachment"
          className="border border-black"
          type="text"
          value={attachment}
          onChange={(e) => setAttachment(e.target.value)}
          required
        />
        <label htmlFor="image">Image</label>
        <input
          id="image"
          className=""
          type="file"
          onChange={(e) => setImage(e.target.files[0])}
          required
        />
        <label htmlFor="category">Category</label>
        <select
          name=""
          value={object}
          onChange={(e) => setObject(e.target.value)}
        >
          {categories.map((category) => (
            <option key={category.id} value={category.id}>
              {category.name}
            </option>
          ))}
        </select>

        <h1>Image Processing</h1>
        <label htmlFor="technique">Technique</label>
        <input
          id="technique"
          className="border border-black"
          type="text"
          value={technique}
          onChange={(e) => {
            setTechnique(e.target.value);
            setIsTechniqueFilled(!!e.target.value);
          }}
        />
        {isTechniqueFilled && (
          <>
            <label htmlFor="value">Value</label>
            <input
              id="value"
              className="border border-black"
              type="text"
              value={value}
              onChange={(e) => setValue(e.target.value)}
              required
            />
          </>
        )}
        <button type="submit">Add</button>
      </form>
    </div>
  );
};
