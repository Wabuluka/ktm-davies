import {
  PropsWithChildren,
  createContext,
  useContext,
  useMemo,
  useReducer,
} from 'react';
import { BookStoreOnBookForm } from '../Types';

type State = BookStoreOnBookForm[];

type Action =
  | { type: 'set'; bookstores: State }
  | { type: 'add'; bookstore: BookStoreOnBookForm }
  | {
      type: 'update';
      bookstore: BookStoreOnBookForm;
      id: BookStoreOnBookForm['id'];
    }
  | {
      type: 'update-primary';
      id: BookStoreOnBookForm['id'];
    }
  | {
      type: 'unset-primary';
    }
  | {
      type: 'delete';
      id: BookStoreOnBookForm['id'];
    };

const reorder = (bookstores: State) =>
  bookstores.sort((a, b) => Number(a.id) - Number(b.id));

const bookStoreReducer = (state: State, action: Action): State => {
  switch (action.type) {
    case 'set':
      return reorder(action.bookstores);
    case 'add':
      return reorder([...state, action.bookstore]);
    case 'update':
      return reorder(
        state.map((bookstore) =>
          bookstore.id === action.id ? action.bookstore : bookstore,
        ),
      );
    case 'update-primary':
      return state.map((bookstore) =>
        bookstore.id === action.id
          ? { ...bookstore, is_primary: true }
          : { ...bookstore, is_primary: false },
      );
    case 'unset-primary':
      return state.map((bookstore) => ({
        ...bookstore,
        is_primary: false,
      }));
    case 'delete':
      return state.filter((bookstore) => bookstore.id !== action.id);
  }
};

export const BookStoresContext = createContext<{
  bookstores?: State;
  selectedStoreIds?: number[];
  primaryStore?: BookStoreOnBookForm;
}>({});

export const BookStoresDispatchContext = createContext<
  React.Dispatch<Action> | undefined
>(undefined);

export function useBookStores() {
  return useContext(BookStoresContext);
}

export function useBookStoresDispatch() {
  return useContext(BookStoresDispatchContext);
}

export const BookStoreDrawerProvider = ({
  initialState = [],
  children,
}: PropsWithChildren<{ initialState?: State }>) => {
  const [bookstores, dispatch] = useReducer(bookStoreReducer, initialState);
  const selectedStoreIds = useMemo(
    () => bookstores.map((bookstore) => Number(bookstore.id)),
    [bookstores],
  );
  const primaryStore = useMemo(
    () => bookstores.find((bookstore) => bookstore.is_primary),
    [bookstores],
  );

  return (
    <BookStoresContext.Provider
      value={{
        bookstores,
        selectedStoreIds,
        primaryStore,
      }}
    >
      <BookStoresDispatchContext.Provider value={dispatch}>
        {children}
      </BookStoresDispatchContext.Provider>
    </BookStoresContext.Provider>
  );
};
