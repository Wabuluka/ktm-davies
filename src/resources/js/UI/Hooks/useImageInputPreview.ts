import { useCallback, useRef, useState } from 'react';

// https://qiita.com/hirokimituya/items/8d1d8c75048ced3f4394
export const useImageInputPreview = (initialImagePreview?: string) => {
  const [imagePreview, setImagePreview] = useState<
    string | ArrayBuffer | null | undefined
  >(initialImagePreview);
  const imageRef = useRef<HTMLInputElement>(null);

  const setImage = useCallback((image: File): void => {
    setImagePreview(undefined);

    const reader = new FileReader();

    reader.onload = (e) => {
      setImagePreview(e.target?.result);
    };

    reader.readAsDataURL(image);
  }, []);

  const unsetImage = useCallback(() => {
    setImagePreview(undefined);
    if (imageRef.current) {
      imageRef.current.value = '';
    }
  }, []);

  return {
    imagePreview,
    setImage,
    unsetImage,
    imageRef,
  };
};
