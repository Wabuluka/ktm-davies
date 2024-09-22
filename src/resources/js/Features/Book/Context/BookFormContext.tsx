import { PropsWithChildren, createContext, useContext } from 'react';
import { useBookForm } from '../Hooks/useBookForm';
import { BookFormData } from '../Types';

type BookForm = ReturnType<typeof useBookForm>;

type Props = {
  initialValues: BookFormData;
};

export const BookFormStateContext = createContext(
  {} as Pick<BookForm, 'data' | 'errors' | 'processing' | 'isDirty'>,
);
export const SubmitBookFormDataContext = createContext(
  {} as Pick<BookForm, 'storeBook' | 'updateBook'>,
);
export const SetBookFormDataContext = createContext(
  {} as Pick<BookForm, 'setData'>,
);

export function useBookFormState() {
  return useContext(BookFormStateContext);
}
export function useSubmitBookFormData() {
  return useContext(SubmitBookFormDataContext);
}
export function useSetBookFormData() {
  return useContext(SetBookFormDataContext);
}

export function BookFormProvider({
  initialValues,
  children,
}: PropsWithChildren<Props>) {
  const { setData, storeBook, updateBook, ...rest } = useBookForm({
    initialValues,
  });

  return (
    <BookFormStateContext.Provider value={rest}>
      <SubmitBookFormDataContext.Provider value={{ storeBook, updateBook }}>
        <SetBookFormDataContext.Provider value={{ setData }}>
          {children}
        </SetBookFormDataContext.Provider>
      </SubmitBookFormDataContext.Provider>
    </BookFormStateContext.Provider>
  );
}
