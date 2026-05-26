import type { FaqItem, HomeDictionary } from "@/content/types";
import { OpenDrawerButton } from "@/components/lead/OpenDrawerButton";

type FAQProps = {
  title: string;
  faqs: FaqItem[];
  dictionary?: HomeDictionary;
};

export function FAQ({ title, faqs, dictionary }: FAQProps) {
  return (
    <section className="bg-neutral-50 py-20" id="faq">
      <div className="section-shell">
        <div className="mb-10 text-center">
          <p className="mb-3 text-sm font-bold uppercase tracking-widest text-teal-700">
            Preguntas frecuentes
          </p>
          <h2 className="text-3xl font-black text-neutral-950 sm:text-4xl">{title}</h2>
        </div>

        <div className="mx-auto max-w-3xl divide-y divide-neutral-200 rounded-2xl border border-neutral-200 bg-white shadow-sm">
          {faqs.map((faq, index) => (
            <details className="group px-6 py-5" key={faq.question} open={index === 0}>
              <summary className="flex cursor-pointer list-none items-center justify-between gap-4 text-base font-black text-neutral-950 marker:hidden [&::-webkit-details-marker]:hidden">
                {faq.question}
                <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-neutral-100 text-teal-700 transition group-open:rotate-45 group-open:bg-teal-50">
                  <svg aria-hidden="true" fill="none" height="16" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" viewBox="0 0 24 24" width="16">
                    <path d="M12 5v14M5 12h14" />
                  </svg>
                </span>
              </summary>
              {dictionary && index === 2 ? (
                <div className="mt-4 text-neutral-600">
                  <p className="leading-7">{dictionary.faq.a3}</p>
                  <ul className="mt-3 list-disc space-y-2 pl-5 leading-7">
                    {[1, 2, 3, 4, 5].map((item) => (
                      <li key={item}>{dictionary.faq[`a3.i${item}`]}</li>
                    ))}
                  </ul>
                </div>
              ) : (
                <p className="mt-4 leading-7 text-neutral-600">{faq.answer}</p>
              )}
            </details>
          ))}
        </div>

        {/* CTA final */}
        <div className="mt-12 rounded-2xl bg-teal-700 p-8 text-center text-white">
          <h3 className="text-xl font-black">¿Tienes más dudas?</h3>
          <p className="mt-2 text-teal-100">
            Cuéntanos tu caso y te damos una respuesta personalizada.
          </p>
          <OpenDrawerButton className="mt-5 inline-flex rounded-xl bg-[#f5b642] px-7 py-3.5 font-black text-neutral-950 transition hover:bg-[#ffc84a] focus-visible:outline-2 focus-visible:outline-white">
            Quiero que me asesoren
          </OpenDrawerButton>
        </div>
      </div>
    </section>
  );
}
