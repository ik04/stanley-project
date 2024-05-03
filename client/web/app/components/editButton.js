"use client";
import React, { useEffect, useState } from "react";

import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import axios from "axios";

const EditButton = ({
  originalName,
  originalObject,
  originalManufacturer,
  originalModel,
  originalSpecification,
  originalAttachment,
  imageId,
}) => {
  const [name, setName] = useState(originalName);
  const [manufacturer, setManufacturer] = useState(originalManufacturer);
  const [model, setModel] = useState(originalModel);
  const [specification, setSpecification] = useState(originalSpecification);
  const [attachment, setAttachment] = useState(originalAttachment);
  const [categories, setCategories] = useState([]);
  const [object, setObject] = useState(originalObject);
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
      if (!name || !manufacturer || !model || !specification || !attachment) {
        alert("Please fill in all required fields.");
        return;
      }
      //   const formData = new FormData();
      //   formData.append("name", name);
      //   formData.append("manufacturer", manufacturer);
      //   formData.append("model", model);
      //   formData.append("specification", specification);
      //   formData.append("attachment", attachment);
      //   formData.append("object", object);
      const formData = {
        object,
        attachment,
        manufacturer,
        model,
        name,
        specification,
      };
      const resp = await axios.patch(
        `${process.env.NEXT_PUBLIC_DOMAIN_NAME}/api/update-details/${imageId}`,
        formData,
        { withCredentials: true }
      );
      location.reload();
    } catch (err) {
      console.log(err);
    }
  };
  return (
    <Dialog>
      <DialogTrigger>Edit</DialogTrigger>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Edit Details</DialogTitle>
        </DialogHeader>
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
          <button type="submit">Update</button>
        </form>
      </DialogContent>
    </Dialog>
  );
};

export default EditButton;
